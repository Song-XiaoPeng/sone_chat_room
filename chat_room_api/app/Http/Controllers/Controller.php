<?php

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OSS\Core\OssException;
use OSS\OssClient;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const PAGE_SIZE = 15;
    const CAPTCHA_PREFIX = "captcha_";
    const CAPTCHA_CACHE = "redis";
    const CODE_SUCCESS = 1;
    const CODE_FAIL = 0;
    const NOTICE_CHG_PWD = 1;
    const ROLE_ID_FOR_FEE = [1, 2, 3, 4, 5, 6, 7, 8, 9, 11];// 提醒管理员

    /**
     * 获取验证码 重新获取验证码
     * @param $captchaId ,$captchaCode
     * @return bool
     */
    static function verifyCaptchaCode($captchaId, $captchaCode): bool
    {
        $cacheKey = self::CAPTCHA_PREFIX . $captchaId;
        $cachedCode = Cache::store(self::CAPTCHA_CACHE)->get($cacheKey);
        //Cache::forget($cacheKey);
        return $cachedCode == $captchaCode;
    }

    /**
     * 设置图片验证码
     * @param $captchaId
     * @return string 返回图片base64 string
     */
    static function generateCaptchaImage($captchaId): string
    {
        $phraseBuilder = new PhraseBuilder(5, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->setDistortion(false);
        $builder->setIgnoreAllEffects(true);
        $builder->build();
        $cacheKey = self::CAPTCHA_PREFIX . $captchaId;
        Cache::store(self::CAPTCHA_CACHE)->put($cacheKey, $builder->getPhrase(), 5);
        return $builder->inline();
    }

    /**
     * @param array $data 返回json 数据体
     * @param int $code_status 返回 状态
     * @param string $message 消息
     * @param \Illuminate\Http\Request|null $request 请求 用于debug
     * @return \Illuminate\Http\JsonResponse  json返回
     */
    static function jsonReturn($data = [], int $code_status = self::CODE_SUCCESS, string $message = '', int $httpStatusCode = 200)
    {
        $json['status'] = $code_status ? $code_status : 0;
        $json['data'] = $data;
        $json['msg'] = $message;
        if (config('app.debug')) {
            $json['debug_sql'] = DB::getQueryLog();
        }
        return response()->json($json, $httpStatusCode);
    }


    /**
     * 适用于直接输出 不适合return的情况 默认是错误 并中止执行
     */
    static function jsonEcho(string $message = '', int $httpStatusCode = 555, $data = [], int $code_status = self::CODE_FAIL)
    {
        $json['status'] = $code_status;
        $json['data'] = $data;
        $json['msg'] = $message;
        header("HTTP/1.0 555 Unauthorized");
        echo json_encode($json);
        die;
    }


    static function jsonPaginationReturn(Builder $query)
    {
        $page = request()->input('size', self::PAGE_SIZE);
        $data = $query->paginate($page);
        return self::jsonReturn($data);
    }

    static function generateCacheKeyByReqeust()
    {
        $request = request();
        $uri = $request->getUri();

        return $uri . '.' . http_build_query($request->all());
    }

    //导出excel
    static function excelExport($rs, $column_arr = [], $title_arr = [], $excel_name = 'export')
    {
        if (empty($rs)) return '';
        $data = [];
        foreach ($rs as $k => $v) {
            foreach ($column_arr as $c_k => $c_v) {
                switch (count($c_v)) {
                    case 1:
                        if (!array_key_exists($c_v, $v)) {
                            unset($column_arr[$c_k], $title_arr[$c_k]);//删除掉不需要或没有权限的字段
                            continue;
                        }
                        $data[$k][] = $v[$c_v];
                        break;
                    case 2:
                        if (!array_key_exists($c_v[0], $v)) {
                            unset($column_arr[$c_k], $title_arr[$c_k]);//删除掉不需要或没有权限的字段
                            continue;
                        }
                        $data[$k][] = isset($v[$c_v[0]][$c_v[1]]) ? $v[$c_v[0]][$c_v[1]] : '';
                        break;
                    case 3:
                        if (!array_key_exists($c_v[0], $v)) {
                            unset($column_arr[$c_k], $title_arr[$c_k]);//删除掉不需要或没有权限的字段
                            continue;
                        }
                        $data[$k][] = isset($v[$c_v[0]][$c_v[1]][$c_v[2]]) ? $v[$c_v[0]][$c_v[1]][$c_v[2]] : '';
                        break;
                    default:
                        break;
                }
            }
        }
        array_unshift($data, $title_arr);
        //PHPExcel_Shared_String 改了源码 gb2312!!!
        Excel::create($excel_name, function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->setStyle(array(
                    'font' => array(
                        'size' => 10,
                    )
                ));
                $sheet->setHeight(1, 20);
                $sheet->freezeFirstRow();
                $sheet->rows($data);
            });
        })->export('xlsx');//如果是默认的xls则不支持表情 有bug
    }

    /**
     * 阿里oss上传
     * @param $object
     * @param $content
     * @param string $type
     * @return bool|string
     */
    static function ossUpload($object, $content, $type = "")
    {
        $accessKeyId = "LTAI752rbfc4enCB";
        $accessKeySecret = "4H0xk2HVcoHvOBPUzJ4XntfISL8W8t";
        $endpoint = "http://oss-cn-shenzhen.aliyuncs.com";
        $bucket = env("OSS_BUCKET_NAME");
        $content = $content;
        if ($type) {
            $object = $type . "/" . $object;
        }
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->putObject($bucket, $object, $content);
        } catch (OssException $e) {
            return false;
        }

        return env("OSS_BUCKET_URL") . $object;
    }

    /**
     * 修改密码时将notice_type置为1
     * @param $notice_type
     * @return int|string
     */
    protected function chgPwdNoticeType($notice_type)
    {
        $old_notice_type = $notice_type;
        if ($old_notice_type) {
            $old_notice_type_arr = explode(',', $old_notice_type);
            if (array_search(self::NOTICE_CHG_PWD, $old_notice_type_arr) === false) {
                array_push($old_notice_type_arr, self::NOTICE_CHG_PWD);
                $old_notice_type = implode(',', $old_notice_type_arr);
            }
        } else {
            $old_notice_type = self::NOTICE_CHG_PWD;
        }
        return $old_notice_type;
    }
}
