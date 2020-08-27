<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <?php include(plugin_dir_path(__FILE__) . 'menu.php'); ?>
        </div>
    </div>

    <div class="smmposting-container">
            <div class="row">

                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-posts">
                                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account" class="form-horizontal">
                                        <ul class="nav nav-tabs">
                                            <li id="href-cron" class="active"><a href="#tab-cron" data-toggle="tab"><?php echo $text_cron; ?></a></li>
                                            <li id="href-license"><a href="#tab-license" data-toggle="tab"><?php echo $text_contacts; ?></a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab-cron">
                                                <div class="alert alert-info"><i class="fa fa-info-circle"></i>
                                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                                    <b><?php echo $text_cron_title; ?></b> <br>
                                                    <?php echo $text_cron_comand; ?>
                                                </div>
                                                <input type="text" id="text" readonly value='wget -U firefox -r -np -q "<?php echo $cron_link; ?>"' class="form-control" />
                                            </div>

                                            <div class="tab-pane" id="tab-license">
                                                <h4>SMM-posting.ru</h4>

                                                <?php echo $text_smmposting_about_1; ?><br>
                                                <br>
                                                <a target="_blank" href="https://smm-posting.ru/?utm_campaign=module-wordpress&utm_source=wordpress.org"><i class="fa fa-share-square-o"></i>  <?php echo $text_redirect_link; ?></a>
                                                |
                                                <a target="_blank" href="mailto:support@smm-posting.ru"><i class="fa fa-envelope-o"></i>  <?php echo $text_send_mail; ?></a>

                                                <hr>
                                                <h4><?php echo $text_we_in_social; ?></h4>
                                                <?php echo $text_smmposting_about_2; ?>
                                                <div class="mt-2">
                                                    <a target="_blank" href="https://ok.ru/group/56305777508594" class="btn btn-warning"><i class="fab fa-odnoklassniki"></i></a>
                                                    <a target="_blank" href="https://vk.com/smm_posting_ru" class="btn btn-vk"><i class="fab fa-vk"></i></a>
                                                    <a target="_blank" href="https://t.me/smm_posting" class="btn btn-info"><i class="fab fa-telegram"></i></a>
                                                    <a target="_blank" href="https://www.instagram.com/smmposting/" class="btn btn-instagram"><i class="fab fa-instagram"></i></a>
                                                    <a target="_blank" href="https://facebook.com/groups/smmposting/" class="btn btn-facebook"><i class="fab fa-facebook"></i></a>
                                                    <a target="_blank" href="https://twitter.com/smm_posting_ru/" class="btn btn-twitter"><i class="fab fa-twitter"></i></a>
                                                    <a target="_blank" href="https://tumblr.com/blog/smmposting/" class="btn btn-tumblr"><i class="fab fa-tumblr"></i></a>
                                                </div>
                                          </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
        </div>
</div>