### 服务中心 ###
#### SMC ——服务监控中心 ####
##### GIT分支管理 #####
 * 主分支
```
monitor.1.0.1
```
 * 开发分支
```
dev-monitor.1.0.1
```
 * 测试及发布tag
```
SMC_YYYYMMDD_VX
```

##### Required #####
 * Google protobuf >= 3.0
 * php_protobuf >= 3.5.1.1
 * php >= 5.6
 * php_redis *
 * php_swoole >= 1.8
 * composer
 
##### composer install #####
```
composer require "google/protobuf"
```

##### command #####
```
/usr/local/bin/php [path]/service monitor kernel
```

##### protocol #####
| 包类型  | 命令  | 包体长度  | 正文 |
| ------------ | ------------ | ------------ | ------------ |
| type  | cmd  | len  | content  |
| Int32  | Int64  | Int32  | byte[]  |
| 4Byte  |  8Byte |  4Byte | nByte  |

###### type格式 ######

| 类型  | 值  |
| ------------ | ------------ |
|  心跳包 | (0x5842 << 16) + (1 << 15)  |
| 服务注册  | (0x5842 << 16) + (1 << 14)  |

###### cmd格式 ######

| 类型  | 值  |
| ------------ | ------------ |
|  PING | 1 << 62  |
| PONG  | (1 << 62) >> 1  |
| REGISTER  | 1 << 61  |

###### len格式 ######

| 类型  | 值  |
| ------------ | ------------ |
|  心跳包 | 0  |
| 服务注册  | 报文正文长度  |

###### 正文 ######

| 类型  | 值  |
| ------------ | ------------ |
|  心跳包 | 空(0byte)  |
| 服务注册  | json  |

##### 说明 #####
1、各服务在部署时，需要首先在SMC中注册，才可以对外提供服务。
```
可以在服务启动时，采用tcp短连接的方式发送注册服务数据包给到SMC
数据包格式请参考protocol部分的register
```
2、各服务需要开启独立的进程或线程以响应SMC服务发送的心跳包，需要回应符合协议格式要求的PONG数据包