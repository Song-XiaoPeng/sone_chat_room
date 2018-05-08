<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/5/7
 * Time: 14:30
 */

class Container
{
    protected $binds;

    protected $instances;

    public function bind($abstract, $concrete)
    {
        if ($concrete instanceof Closure) {
            $this->binds[$abstract] = $concrete;
        } else {
            $this->instances[$abstract] = $concrete;
        }
    }

    public function make($abstract, array $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }
}

class SuperModuleInterface {}

class Superman {
    public function __construct(SuperModuleInterface $module)
    {
        $this->module = $module;
    }
}
class XPower {
}

// 创建一个容器（后面称作超级工厂）
$container = new Container();

// 向该 超级工厂添加超人的生产脚本
$container->bind('superman', function($container, $moduleName) {
    return new Superman($container->make($moduleName));
});
// 向该 超级工厂添加超能力模组的生产脚本
$container->bind('xpower', function($container) {
    return new XPower;
});

/**
 * $this->binds = [
 *  'superman' => function($container, $moduleName){  return new Superman($container->make($moduleName));  },
 *  'xpower'   => function($container){  return new XPower;  },
 * ]
 */
// ****************** 华丽丽的分割线 **********************
// 开始启动生产
$superman_1 = $container->make('superman', ['xpower']);
//等价于
$superman_1 = new Superman(new XPower());
/**
 *  $this = new Container();
 *  call_user_func_array($closure,[$this,'xpower']) =>
 *
 *  1. 执行了该匿名函数
 *  function($this, 'xpower'){
 *    return new Superman($this->make('xpower'));
 *
 *  }
 *  2. 然后执行该方法
 *  $this->make('xpower',[$this])
 *  function($this){
 *    return new XPower;
 *
 *  }
 *  3. 即执行了
 *  return new Superman(new XPower());
 *
 *
 *
 */

$superman_2 = $container->make('superman', 'ultrabomb');
$superman_3 = $container->make('superman', 'xpower');
// ...随意添加