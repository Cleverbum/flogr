    <div id="footer">
      <?php
      if ($photo && $photo->get_userId() == FLICKR_USER_ID) {
        echo "Photo &copy;&nbsp;";
        $photo->userlink();
        echo "&nbsp;|&nbsp;";        
      }
      ?>Site powered by <a href="https://github.com/Cleverbum/flogr">Flogr v3</a> &amp; <a href="http://flickr.com"><span style="color: rgb(0, 102, 204);">Flick</span><span style="color: rgb(255, 0, 153);">r</span></a>
      </div>
    </body>
</html>

