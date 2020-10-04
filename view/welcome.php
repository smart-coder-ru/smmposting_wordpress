<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <img width="200px" src="<?php echo SMMP_PLUGIN_URL .('/view/image/smm_posting_logo.svg'); ?>">
            <h1 class="smm-heading-title"><span style="float:right; font-size: 10px; margin-left:5px;" class="label label-success"><?php echo $version; ?></span> </h1><br>
        </div>
    </div>

    <div>
        <div class="smmposting-container">
            <h1 class="smm-heading-title"><?php echo $text_welcome_title; ?></h1>
            <?php echo $text_welcome_description; ?>

            <div class="socials-grey">
                <i class="fab fa-vk"></i>
                <i class="fab fa-odnoklassniki"></i>
                <i class="fab fa-telegram"></i>
                <i class="fab fa-facebook"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-tumblr"></i>
                <i class="fab fa-twitter"></i>
            </div>

            <h2 class="smm-heading-title mt-2"><?php echo $text_welcome_start; ?></h2>
            <?php echo $text_welcome_instruction_1; ?> <a href="https://smm-posting.ru/register?utm_campaign=module-wordpress&utm_source=wordpress.org">https://smm-posting.ru/register</a> <br>
            <?php echo $text_welcome_instruction_2; ?> <a href="https://smm-posting.ru/settings?utm_campaign=module-wordpress&utm_source=wordpress.org">https://smm-posting.ru/settings</a> <br>
            <?php echo $text_welcome_instruction_3; ?> <br>
            <?php echo $text_welcome_instruction_4; ?> <br>

            <div class="mt-2">
                <?php if ($error_warning) { ?>
                    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>

                <?php if ($success) { ?>
                    <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php } ?>

                <form action="<?php echo $welcome_link; ?>" method="POST">
                    <div class="form-group">
                        <label for="domain" class="form-label"><?php echo $text_domain; ?></label>
                        <input id="domain" readonly value="<?php echo $domain; ?>" class="form-control" type="text" placeholder="Введите ваш API токен">
                    </div>
                    <div class="form-group">
                        <label for="api_token" class="form-label"><?php echo $text_api_token; ?></label>
                        <input id="api_token" name="config[api_token]" value="<?php echo $api_token; ?>" class="form-control" type="text" placeholder="Введите ваш API токен">
                    </div>
                    <button class="btn btn-info mt-2" id="connect"><?php echo $text_connect; ?></button>
                </form>

            </div>
        </div>
    </div>
</div>