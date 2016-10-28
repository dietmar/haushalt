# haushalt

**haushalt** is a little PHP web application used for tracking household expenses, for example among student roommates (which is what I wrote it for).

To try it in a docker container (a containerized environment):

```bash
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

Then open [http://localhost:18000/haushalt/](http://localhost:18000/haushalt/) in your browser.

For real use you should modify `haushalt/program/Config.php`.

It looks like this:
![Screenshot](https://github.com/dietmar/haushalt/blob/master/screenshot.png)
