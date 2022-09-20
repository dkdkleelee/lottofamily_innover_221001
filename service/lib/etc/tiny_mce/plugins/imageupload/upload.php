<?php
//▶ Load default configuration

############### 디렉토리 출력 환경설정 ###############
$userFolder = $_SESSION['ss_mb_id'] ? $_SESSION['ss_mb_id'] : "guest";
$userRootDir = ($userRootDir) ? $userRootDir:"../../../../../upload/editor_images";
$replaceDir = ($replaceDir) ? $replaceDir:"";

$workDir = ($cdir) ? checkUrl($cdir) : $userRootDir;   //작업 디렉토리... 레지스트리에 등록
$workDir = $workDir."/".$userFolder."/";
if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"])) {
  //@todo Change base_dir!

  if(!is_dir($workDir)) {
	mkdir($workDir);
	chmod($workDir, 0707);
  }
  $base_dir = $workDir;
  //@todo Change image location and naming (if needed)
  $image = $base_dir . $_FILES["image"]["name"];
  move_uploaded_file($_FILES["image"]["tmp_name"], $image);
?>
<input type="text" id="src" name="src" />
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script>
  var ImageDialog = {
    init : function(ed) {
      ed.execCommand('mceInsertContent', false, 
        tinyMCEPopup.editor.dom.createHTML('img', {
          src : '<?php echo $image; ?>'
        })
      );
      
      tinyMCEPopup.editor.execCommand('mceRepaint');
      tinyMCEPopup.editor.focus();
      tinyMCEPopup.close();
    }
  };
  tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);
</script>
<?php  } else {?>
<form name="iform" action="" method="post" enctype="multipart/form-data">
  <input id="file" accept="image/*" type="file" name="image" /><input type="submit" value="업로드">
</form>
<?php }?>