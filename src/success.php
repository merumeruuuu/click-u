<?php if (count($success) > 0) : ?>
 <div class="succezz" id="mydiv">
     <?php foreach ($success as $succesz) : ?>
       <tr>
          <td><?php echo $succesz ?> .. </td>
       </tr>
       <?php

 ?>
<?php endforeach ?>
 </div>
<script type="text/javascript">
setTimeout(function() {
  $('#mydiv').fadeOut('fast');
}, 5000); // <-- time in milliseconds
</script>
<?php  endif ?>
