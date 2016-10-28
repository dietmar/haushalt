
To try it in a docker container (a containerized environment):

```
git clone git@github.com:dietmar/haushalt.git
cd haushalt

docker run \
 -dit \
 --name haushalt \
 -p 18000:80 \
 -v "$(pwd)"/haushalt:/var/www/html/haushalt \
 php:7.0-apache \
 /var/www/html/haushalt/program/setup.bash
```

Then open http://localhost:18000/haushalt in your browser.
