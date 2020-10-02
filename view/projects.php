<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <?php include(SMMP_PLUGIN_DIR . 'view/menu.php'); ?>
        </div>
    </div>

    <div class="smmposting-container">
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
        <div class="row">
            <div class="col-sm-12 clearfix">
                <div class="pull-right">
                    <a href="<?php echo $add_project_link; ?>" style="margin-bottom:2rem;" class="btn btn-success btn-md"><i class="fa fa-plus"></i> <span class="hidden-xs"><?php echo $button_add_project;?></span></a>
                </div>
            </div>

            <div class="col-sm-12 clearfix">
                <div class="row  row-flex">
                    <?php if (!empty($smm_projects)) { ?>
                        <?php foreach( $smm_projects as $smm_project ) { ?>
                            <div class="col-sm-4">
                                <div class="card smm-project">
                                    <div class="card-header">
                                        <h4><?php echo $smm_project['name'];?></h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php if (isset($smm_project['socials']) && is_array($smm_project['socials'])) { ?>
                                                    <?php if (array_key_exists('ok',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-odnoklassniki"></i></a>
                                                    <?php } ?>


                                                    <?php if (array_key_exists('vk',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-vk"></i></a>
                                                    <?php } ?>

                                                    <?php if (array_key_exists('tg',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-telegram"></i></a>
                                                    <?php } ?>

                                                    <?php if (array_key_exists('ig',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-instagram"></i></a>
                                                    <?php } ?>

                                                    <?php if (array_key_exists('fb',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-facebook"></i></a>
                                                    <?php } ?>

                                                    <?php if (array_key_exists('tb',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-tumblr"></i></a>
                                                    <?php } ?>

                                                    <?php if (array_key_exists('tw',$smm_project['socials'])) { ?>
                                                        <a class="socials"><i class="fab fa-twitter"></i></a>
                                                    <?php } ?>

                                                <?php } ?>


                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <a class="btn btn-info" href="<?php echo $edit_project_link; ?>&id=<?php echo $smm_project['id']; ?>"><i class="fa fa-edit"></i> <?php echo $text_preview; ?></a>
                                            <a class="btn btn-danger" href="<?php echo $deleteproject_link; ?>&id=<?php echo $smm_project['id']; ?>"><i class="fa fa-trash"></i> <?php echo $text_delete; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="text-center">
                            <div class="row">
                                <a href="<?php echo $add_project_link; ?>" title="<?php echo $button_add_project;?>">
                                    <img src="/admin/view/image/smmposting/smm.gif">
                                </a>
                            </div>

                            <a href="<?php echo $add_project_link; ?>" style="margin-bottom:2rem;" class="btn btn-success btn-md"><i class="fa fa-plus"></i> <span class="hidden-xs"><?php echo $button_add_project;?></span></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
            <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
    </div>