<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Url;
use dosamigos\tinymce\TinyMce;
/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="template-form">
    <form id="bulk-mail-out-form" action="<?= Url::to(['/email-template/insert']) ?>"  method="post" enctype="multipart/form-data">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>"/>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Template Type <span class="text-danger">*</span></label>
                <select class="form-control" name="template_type">
                    <option value="">Choose Template Type</option>
                    <option value="email-template" <?php if($getData->template_type=='email-template') echo 'selected';?>>Email Template</option>
                    <option value="bulletin-template" <?php if($getData->template_type=='bulletin-template') echo 'selected';?>>Bulletin Template</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label>Template Name <span class="text-danger">*</span></label>
                <input type="text" value="<?= $getData->template_name;?>" name="template_name" class="form-control" placeholder="Template Name">
            </div>
            <div class="col-md-6 form-group">
                <label>Email Subject <span class="text-danger">*</span></label>
                <input type="text" name="email_subject" value="<?= $getData->email_subject;?>" class="form-control" placeholder="Email Subject">
            </div>
            <div class="col-md-6 form-group">
                <label>Bulletin Heading </label>
                <input type="text" name="bulletin_heading" class="form-control" value="<?= $getData->bulletin_heading;?>" placeholder="Bulletin Heading">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <label>Variables</label> <br>
                <span class="add_merge_field mt-1">{{full_name}}</span>,
                <span class="add_merge_field mt-1">{{email_address}}</span>
            </div>
        </div>
        <div class="row">
            <div id="body">
                <div class="col-md-12 form-group">
                    <label for="body1">Body</label>
                    <textarea class="form-control" name="content" id="summernote"><?= $getData->content;?></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <button class="btn btn-success" type="submit">Save</button>
                <button class="btn btn-danger" id="preview-email" type="button">Preview</button>
                <div class="modal fade" id="previewTemplate1Modal" tabindex="-1" role="dialog" aria-labelledby="previewTemplate1Modal" aria-hidden="true">
                    <?= $this->render('_preview_template_modal') ?>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="content_box_data" style="display: none;"><?php echo $getData->content;?></div>
<script type="text/javascript">
    $(document).on('click', '#preview-email', function() {
        var body = $('#summernote').val();
        document.getElementById('email-template-body').innerHTML = body;
        $('#previewTemplate1Modal').modal('toggle');
    });
    $('#summernote').summernote({
        height:400
    });
</script>