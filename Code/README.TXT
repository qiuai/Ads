# 广告系统
192.168.1.175		www.ads.me ads.me s.ads.me a.ads.me c.ads.me img.ads.me 


############################
# ads.me   广告系统	   #
############################
# 主站
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Dynamic/
    ServerName www.ads.me 
    ServerAlias ads.me
</VirtualHost>

# 管理后台
<VirtualHost *:80>
    DocumentRoot /data/wwwroot/web/ads.cc/Code/Www/Dynamic/
    ServerName a.ads.me  
        # 站点图片别名设置
        Alias /statics /data/wwwroot/web/ads.cc/Code/Www/Statics/        
        # 附件预览
        Alias /Uploadfile /data/wwwroot/web/ads.cc/Uploadfile/        
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