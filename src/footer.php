

<br>
<footer class="footer">
    <div class="container-fluid">
        <nav class="pull-left">
            <ul>
                <li>
                    <a href="#">
                        Meru
                    </a>
                </li>
                <li>
                    <a href="#">
                        Ching
                    </a>
                </li>
                <li>
                    <a href="#">
                        Dar
                    </a>
                </li>
                <li>
                    <a href="#">
                       Jen
                    </a>
                </li>
            </ul>
        </nav>
        <p class="copyright pull-right">
            &copy; <script>document.write(new Date().getFullYear())</script> <a href="http://www.creative-tim.com">Creative Tim</a>
        </p>
    </div>
</footer>

</div>
</div>
</body>

<!--   Core JS Files   -->
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

<!--  Charts Plugin -->
<script src="assets/js/chartist.min.js"></script>

<!--  Notifications Plugin    -->
<script src="assets/js/bootstrap-notify.js"></script>


<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->

<script type="text/javascript">
//notifications
$(document).ready(function(){
 function load_unseen_notification(view = '')
 {
  $.ajax({
   url:"notification.php",
   method:"POST",
   data:{view:view},
   dataType:"json",
   success:function(data)
   {
     $('.dropdown-menu').html(data.notification);
    if(data.unseen_notification > 0)
    {
     $('.count').html(data.unseen_notification);
    }
   }
  });
 }
 load_unseen_notification();
 $(document).on('click', '.notif-count', function(){
  $('.count').html('');
  load_unseen_notification('yes');

 });
 setInterval(function(){
  load_unseen_notification();
}, 1000);

});
</script>
<?php
print str_pad('',4096)."\n";
ob_flush();
flush();
set_time_limit(45);
?>
</html>
