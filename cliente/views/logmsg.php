<div style="width: 100%; height: 400px; margin-top: 0px;">
    <p style="padding: 0px; margin: 0px; margin-top: 5px; text-align: center; font-size: 20px;">Log</p>
    <div style="width: 750px; height: 320px; margin: auto; margin-top: 5px;">
        <textarea name="logcliente" id="logcliente" readonly wrap="hard" style="resize: none; height: 100%; width: 100%; font-size: 17px;"><?php if(isset($_SESSION['logcliente'])){echo str_replace('/n','&#13;&#10;',$_SESSION['logcliente']);}else{echo '';}?></textarea>
    </div>
</div>