Options +Indexes
IndexOptions FancyIndexing FoldersFirst NameWidth=* DescriptionWidth=*
RewriteEngine on
RewriteCond $1 !^(index\.php|public|assets|images|robots\.txt)
RewriteRule ^(.*)$ /index.php/$1 [L]
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept"
Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
Header add Access-Control-Max-Age 86400
