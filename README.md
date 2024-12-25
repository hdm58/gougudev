## 📐 勾股DEV3.0
![输入图片说明](https://dev.gougucms.com/storage/202204/banner.jpg)

### ✅ 相关链接
- 系统网站：https://www.gougucms.com/home/pages/detail/s/gougudev.html
- 文档地址：[https://blog.gougucms.com/home/book/detail/bid/7.html](https://blog.gougucms.com/home/book/detail/bid/7.html)
- 项目会不定时进行更新，**建议⭐star⭐和👁️watch👁️一份**。

### ⭕ 开源项目
1. [开源项目系列：勾股OA —— OA协同办公系统框架](https://gitee.com/gougucms/office)
2. [开源项目系列：勾股CMS —— CMS内容管理系统框架](https://gitee.com/gougucms/gougucms)
3. [开源项目系列：勾股BLOG —— 个人&工作室博客系统](https://gitee.com/gougucms/blog)
4. [开源项目系列：勾股DEV —— 项目研发管理系统](https://gitee.com/gougucms/dev)
5. [开源项目系列：勾股Admin —— 基于Layui的Web UI解决方案](https://gitee.com/gouguopen/guoguadmin)

### 📋 系统介绍
勾股DEV是一款专为IT研发团队打造的项目管理与团队协作的系统工具，可以在线管理团队的工作、项目和任务，覆盖从需求提出到研发完成上线整个过程的项目协作。

勾股DEV的产品理念：通过“项目（Project）”的形式把成员、需求、任务、缺陷(BUG)、文档、互动讨论以及各种形式的资源组织在一起，团队成员参与更新任务、文档等内容来推动项目的进度，同时系统利用时间线索和各种动态的报表的形式来自动给成员汇报项目进度。

![输入图片说明](https://dev.gougucms.com/storage/202204/flow.png)


### ✳️ 演示地址

勾股DEV演示地址：[https://dev.gougucms.com](https://dev.gougucms.com)

沟通咨询请加微信号：hdm588

PS：为了给后面的人提供良好的演示体验，体验以查看为主。

**登录账号及密码：**
~~~
   技术总监：gougujishu     123456
   工 程 师：ligong    123456
   产品经理：ouyangchanpin        123456
~~~

### ✴️ 系统特点
- 多产品支持，可添加多产品管理
- 多项目支持，可以多项目同时进行管理
- 可配置的用户角色控制，不同的角色可配置不同的操作权限
- Wiki 形式的文档撰写，Mardown编辑器，程序员使用高效便捷
- 每个项目配置有需求、任务、Wiki文档、动态记录、互动评论、工作记录模块
- 任务时间跟踪机制，项目任务多状态流转，任务成果可见可控。
- 工时登记，团队精细化管理，可统计每个人每天在每个项目做了多少时间
- 任务安排，任务分配指定人，可设置负责人、多协同人员
- 员工的操作记录全覆盖跟踪


### 📚 安装教程

**一、服务器。**

服务器最低配置：
~~~
    1核CPU (建议2核+)
    2G内存 (建议4G+)
    1M带宽 (建议3M+)
~~~
服务器运行环境要求：
~~~
    PHP >= 7.3  
    Mysql >= 5.5.0 (需支持innodb引擎)  
    Apache 或 Nginx  
    PDO PHP Extension  
    MBstring PHP Extension  
    CURL PHP Extension  
    Composer (用于管理第三方扩展包)
~~~

**二、系统安装**

**命令行安装（推荐）**

推荐使用命令行安装，因为采用命令行安装的方式可以和勾股DEV随时保持更新同步。使用命令行安装请提前准备好Git、Composer。

勾股DEV的安装步骤，以下加粗的内容需要特别留意：  

第一步：克隆勾股DEV到你本地  
    git clone https://gitee.com/gougucms/dev.git

第二步：进入目录  
    cd gougudev  
    
第三步：下载PHP依赖包
    
composer install  
    
第四步：添加虚拟主机并绑定到项目的public目录 ，实际部署中，确保绑定域名访问到的是public目录。**（这一步很重要，很多人出错）**

第五步：伪静态配置 **（这一步也很重要，很多人出错）**，使用的是ThinkPHP的伪静态规则，具体看下面的伪静态配置内容。  

Nginx 修改nginx.conf 配置文件 加入下面的语句。
```
    location / {
        if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
        }
    }
```
Apache 把下面的内容保存为.htaccess文件放到应用入 public 文件的同级目录下。
```
    <IfModule mod_rewrite.c>
    Options +FollowSymlinks -Multiviews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
    </IfModule>
```

第六步：访问 http://www.yoursite.com/install/index 进行安装

⚠️⚠️ **注意：安装过程中，请先手动创建空的数据库，然后填写刚创建的数据库名称和用户名也可完成安装。** ⚠️⚠️

🔺🔺 **提醒：安装过程中，如果进度条卡住，一般都是数据库写入权限或者安装环境配置问题，请注意检查。遇到问题请到QQ群：24641076 反馈** 🔺🔺

✅✅ **PS：如需要重新安装，请删除目录里面 config/install.lock 的文件，即可重新安装。** ✅✅

### ❓ 常见问题

1.  安装失败，可能存在php配置文件禁止了putenv 和 proc_open函数。解决方法，查找php.ini文件位置，打开php.ini，搜索 disable_functions 项，看是否禁用了putenv 和 proc_open函数。如果在禁用列表里，移除putenv proc_open然后退出，重启php即可。

2.  如果安装后打开页面提示 `404`错误，请检查服务器伪静态配置，如果是宝塔面板，网站伪静态请配置使用thinkphp规则。

3.  如果提示当前权限不足，无法写入配置文件`config/database.php`，请检查`config`目录是否可写，还有可能是当前安装程序无法访问父目录，请检查PHP的`open_basedir`配置。

4.  如果`composer install`失败，请尝试在命令行进行切换配置到国内源，命令如下:

    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

5.  访问 http://www.你的域名.com/install/index 前，请注意查看伪静态请配置是否设置了`thinkphp`伪静态规则。

6.  出现访问报错一般是服务器环境配置问题

    比如：伪静态配置，网站的访问入口是否绑定`public`目录，放配置文件的目录是否有可写权限，放缓存的目录是否有可写权限，数据库连接确认无误等。

    开启`debug`的方式请查看链接：https://blog.gougucms.com/home/book/detail/bid/3/id/77.html

    开启`debug`后，看具体的报错信息，然后沿着这些思路去一个个排查基本解决90%的问题。

7.  如果是composer的安装，composer install报错，这不是勾股系列系统的问题，可以百度得到具体解决方案的。

8.  安装过程中，如果 **进度条卡住(99%)**，一般都是数据库写入权限或者安装环境配置`config`目录无法写入问题，请注意检查权限。

9.  如果安装成功后，无法显示图形验证码的，请看是否已安装（开启）了PHP的`GD`库。

10.  如果安装成功后，无法上传文件的，请看是否已安装（开启）了PHP的`fileinfo`扩展。

11.  遇到解决不了的问题请到QQ群反馈：24641076（群一满），46924914（群二名额不多） 。

12. **最后，如果实在安装不成功，确实需要提供安装服务的，请搜索微信号：hdm588，或者QQ号：327725426，添加好友，注意备注[安装勾股系统]。开源不易，该服务需友情赞赏💰99元。**

### 🖼️ 截图预览
|页面截图      |    部分截图|
| :--------: | :--------:|
| ![功能导图](https://dev.gougucms.com/storage/202204/dev1.png "功能导图")|![功能导图](https://dev.gougucms.com/storage/202204/dev2.png "功能导图")|
|![功能导图](https://dev.gougucms.com/storage/202204/dev3.png "功能导图")|![功能导图](https://dev.gougucms.com/storage/202204/dev4.png "功能导图")|

### ⭐ 开源助力
- 勾股DEV遵循GPL-3.0开源协议发布。 
- 开源的系统少不了大家的参与，如果大家在体验的过程中发现有问题或者BUG，请到Issue里面反馈，谢谢！
- 如果觉得勾股DEV不错，不要吝啬您的赞许和鼓励，请给我们⭐ STAR ⭐吧！

### 👍 支持我们
- If the project is very helpful to you, you can buy the author a cup of coffee☕.
- 如果这个项目对您有帮助，请支持一下我们，可以请我们喝杯咖啡哟☕

|支付宝      |    微信|
| :--------: | :--------:|
| <img src="https://www.gougucms.com/static/home/images/zfb.png" width="300"  align=center />|<img src="https://www.gougucms.com/static/home/images/wx.png" width="300"  align=center />|

