**mxAdmin是一款基于ThinkPHP V6.0.7和Layui v2.5.6的快速后台开发框架。纯ThinkPHP6.0写法，无任何封装。**


## 主要特性

* 基于`Auth`权限验证扩展插件
    * 对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证
    * 可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
    * 一个用户可以属于多个用户组，我们需要设置每个用户组拥有哪些规则
    * 支持规则表达式
* 完善的模块化前端 UI 框架
    * 基于`Layui2.5.6`开发
    * 基于`Bootstrap`开发，自适应手机、平板、PC

## 安装使用
> 运行环境要求PHP7.1+
>
* 当前版本使用 ThinkPHP 6.0.7，对PHP运行环境要求PHP7.1+，具体请阅读 ThinkPHP 官方文档
* 项目安装及二次开发可以先阅读`[ThinkPHP6.0完全开发手册]`官方文档，数据库(demo_muxue_com_cn.sql)文件放在项目根目录下
* 项目测试需要自行搭建环境导入数据库(demo_muxue_com_cn.sql)，并修改数据库配置(config/database.php)

## 在线演示

http://demo.muxue.com.cn/mxadmin

用户名：demo

密　码：123456

提　示：演示站数据无法进行修改，请下载源码安装体验全部功能

## 系统截图
![系统截图](http://img.zlm.ennn.cn/20210326/0ff23245c5f64d469e9f8027f220cc70.gif)

## 问题反馈

在使用中有任何问题，请使用以下联系方式聯絡我們

mxAdmin后台框架使用问题交流（QQ群 796536326）

[![mxAdmin后台框架交流群](https://pub.idqqimg.com/wpa/images/group.png)](https://qm.qq.com/cgi-bin/qm/qr?k=2oqMakPGF240Sw6VC3NlDgGCcuLmWhIl&jump_from=webapi)

若在群里没有得到及时回复，请联系我的企业微信。

![企业微信](http://img.zlm.ennn.cn/20210326/edf3a56e7ad2e366d28b6e0e7ea6a9b9.jpg)


## 特别鸣谢

感谢以下的项目，排名不分先后

ThinkPHP：http://www.thinkphp.cn

Layui：https://www.layui.com

Bootstrap：https://www.bootcss.com

jQuery：https://www.jquery123.com

腾讯云：https://cloud.tencent.com

Gitee: https://gitee.com

七牛云: https://www.qiniu.com


## 版权信息

mxAdmin遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

Copyright © 2020-2021 福州目雪科技有限公司 All Rights Reserved.

官方网站：http://www.muxue.com.cn


## 赞助打赏（大佬们赏口饭吃呗）
![赞助](http://img.zlm.ennn.cn/20210326/2b99c457540b866e616687777b2230a1.png)