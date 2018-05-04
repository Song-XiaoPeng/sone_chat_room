import { util } from './tools.js'

let request = util.axiosInstance
let ajax = {}

ajax.login = function(obj) {
    request.post('time_line/doLoginBackend',obj.data)
    .then(function(res){
        if(res.data.code === 0){
            obj.success(res.data.data)
        }else{
            obj.error(res.data.msg)
        }
    })
    .catch(function(error){
        console.log(error)
    })
}

export default ajax;