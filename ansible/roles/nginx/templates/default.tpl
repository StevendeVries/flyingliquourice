server {
    {% if nginx.ssl | default(false) %}
    listen 443 ssl spdy;
    {% else %}
    listen 80;
    {% endif %}

    sendfile off;
    server_tokens off;

    if ($host !~* ^www\.){
        rewrite ^(.*)$ $scheme://www.{{ servername }}$1;
    }

    charset utf-8;

    root {{ nginx.docroot }};
    index index.html index.php;

    server_name {{ nginx.servername }};

    client_max_body_size 5m;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options SAMEORIGIN;
    add_header X-Xss-Protection "1; mode=block" always;

    error_page 404 /404.html;

    error_page 500 502 503 504 /50x.html;
        location = /50x.html {
        root /usr/share/nginx/www;
    }

    {% if nginx.ssl | default(false) %}
    include ssl.conf;
    include ssl-stapling.conf;

    ssl_certificate         /etc/nginx/ssl/{{ servername }}_self_signed.pem;
    ssl_trusted_certificate /etc/nginx/ssl/{{ servername }}_self_signed.pem;
    ssl_certificate_key     /etc/nginx/ssl/{{ servername }}_self_signed.key;
    {% endif %}

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        internal;
    }

    location ~ /\.ht {
        deny all;
    }
}

{% if nginx.ssl | default(false) %}
server {
    listen 80;
    server_name {{ nginx.servername }};
    return 301 https://www.{{ servername }}$request_uri;
}
{% endif %}
