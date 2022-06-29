<?php if (count($errors) > 0) : ?>
 <div class="error"id="mydiv">

     <?php foreach ($errors as $error) : ?>
       <tr>
          <td><?php echo $error ?> .. </td>
       </tr>

<?php endforeach ?>


 </div>
 <script type="text/javascript">
 setTimeout(function() {
   $('#mydiv').fadeOut('fast');
 }, 5000); // <-- time in milliseconds
 </script>
<?php  endif ?>
