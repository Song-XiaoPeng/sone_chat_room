import Bus from './bus.js'

export default class WebDB {
    constructor(height, width){
        this.name = 'Polygon';
        this.height = height;
        this.width = width;
    }
}

export function openDB(dbName,msg) {
    var request = window.indexedDB.open(dbName,3)
    var db

    request.onsuccess = (event) => {
        console.log('数据库打开了')
        db = request.result;
        var transaction = db.transaction(["group_msg"], "readwrite");
        var objectStore = transaction.objectStore("group_msg");

        console.log('添加数据')
        var request1 = objectStore.add(msg);
        request1.onsuccess = function(event) {
        // event.target.result == customerData[i].ssn
        };        
    };

    request.onupgradeneeded = function(){
        //创建表的结构
        var db = request.result;
        console.log("表结构")
        db.createObjectStore("group_msg",{
            keyPath: "sendTime"
            //keyPath:,//无法与autoIncrement联合使用，自己设定的id字段的字段名
            // autoIncrement:"user_id",//指明当前数据id自增长（indexdb）

        })
    }
}

export function createDB(dbName,table,keyPath) {
    var request = window.indexedDB.open(dbName)
    var db
    request.onsuccess = (event) => {
        console.log('数据库打开了')
        db = request.result;
        Bus.$emit('createDB',db)
        console.log(db)
    };    

    request.onupgradeneeded = function(){
        //创建表的结构
        var db = request.result;
        console.log("创建表结构")
        db.createObjectStore(table,{
            keyPath: keyPath
            //keyPath:,//无法与autoIncrement联合使用，自己设定的id字段的字段名
            // autoIncrement:"user_id",//指明当前数据id自增长（indexdb）

        })
    }    
}

export function insertData(db,table,msg) {

    var tran = db.transaction([table],"readwrite")

    //通过事务控制对象获取数据表对象
    var objectStore = tran.objectStore(table)

    var addRequest = objectStore.add(msg)

    addRequest.onsuccess = function(){
        console.log("数据创建成功"+ msg +"")
    }
}

export function getData(db, table) {
    var objectStore = db.transaction([table],"readwrite").objectStore(table)
    // var request = objectStore.getAllKeys()
    objectStore.getAll().onsuccess = function (event){
        switch(table){
            case "group_msg":
                Bus.$emit('flushMsgList',event.target.result)
            break;    
            case "user_list":
                Bus.$emit('user_list',event.target.result)
            break;    
        }
    }
    
    // console.log(request)
    // request.onsuccess = function(event) {
    //     var keys = event.currentTarget.result
    //     console.log(keys)
    //     return keys
    // }
}
