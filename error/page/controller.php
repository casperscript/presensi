<!DOCTYPE HTML>
<html>
    <head>
        <title>Page Not Found</title>
        <link href="<?= $this->getUrl->baseUrl; ?>error/page/css/style.css" rel="stylesheet" type="text/css"  media="all" />
    </head>
    <body>
        <div class="wrap">
            <div class="content">
                <img src="<?= $this->getUrl->baseUrl; ?>error/page/images/error-img.png" title="error" />
                <p><?= $error; ?></p>
            </div>
        </div>
    </body>
</html>

