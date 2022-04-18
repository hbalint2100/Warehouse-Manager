<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->getTitle()??'Base title'; ?></title>
        <meta name="description" content="<?php echo $this->getDescription() ?? 'Base description.'; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo $this->getStyleSheetPath()? "<link rel=\"stylesheet\" href=\"{$this->getStyleSheetPath()}\" type=\"text/css\">" : '';?>
    </head>
    <body><?php include_once $this->getBodyPath() ?? throw new FileNotFoundException("Missing body! - BaseView </body>"); ?></body>
</html>