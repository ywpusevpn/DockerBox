# DockerBox

## 介绍

一套完整的php docker开发环境

包含镜像:

- php5.6
- php7.1
- mongodb
- redis
- memcache
- nginx
- nodejs
- mysql
- phpmyadmin

> 未来会支持更多镜像

特色功能:

支持php-fpm、php-cli。

包含swoole、amqp、inotify、mongodb、msgpack、tideways、yac等常用php扩展

支持laravel、yii2、thinkphp等php-fpm框架

支持基于swoole的框架

## 快速开始

git clone https://github.com/ywpusevpn/DockerBox.git

cd DockerBox

docker-compose up

## 特别说明

有问题或建议，请提issues

本环境不适合小白使用，需要一定的linux php docker基础

## MYSQL开启主从

1.主库执行

CREATE USER 'slave'@'%' IDENTIFIED BY '123456';

2.主库执行

GRANT REPLICATION SLAVE, REPLICATION CLIENT ON *.* TO 'slave'@'%';

3.从库执行

show master status;

4.从库执行

change master to master_host='172.18.0.2',master_port=3306,master_user='slave',master_password='123456',master_log_file='主库的log_file日志文件',master_log_pos=主库的数据复制位置;

5.主库执行

start slave;
