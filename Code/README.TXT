# 广告系统
192.168.1.175		www.ads.me ads.me s.ads.me a.ads.me c.ads.me img.ads.me r.ads.me 

# 账号 / 密码
# 管理员 admin / 123456
# 广告主 admin / 123456
# 网站主 admin / 123456
# 测试   demo  / 123456


############################
# ads.me   广告系统	   #
############################
# 主站
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Dynamic/Ads/
    ServerName www.ads.me 
    ServerAlias ads.me
</VirtualHost>

# 管理后台
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Dynamic/Zhts/
    ServerName a.ads.me       
</VirtualHost>

# 权限系统
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Dynamic/Authority/
    ServerName r.ads.me         
</VirtualHost>

# 静态文件
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Statics/
    ServerName s.ads.me    
</VirtualHost>

# 图片
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Uploadfile/
    ServerName img.ads.me    
</VirtualHost>