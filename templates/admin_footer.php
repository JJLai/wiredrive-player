<div class="hidden">
    <div id="wdp-dialog">
        <div class="wdp-dialog-content">
            <div id="wdp-dialog-message">
                <p>Please enter the address of the mRSS feed you'd like to display. Video feeds may only contain video files. Image feeds may only contain image files (JPG, GIF or PNG).</p>
                <p><strong>Example:</strong></p>
                <p><code>http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d</code></p>
            </div>
            <p><input type="text" id="wdp-dialog-feed" style="width:98%" /></p>
            <p><strong>Set dimensions for your player:</strong></p>
            <p>Leave blank to use the default dimensions; select <strong>Wiredrive Player</strong> from the <strong>Settings menu</strong> in WordPress to set default dimensions.</p>
            <p><input type="text" id="wdp-dialog-width" class="wdp-dialog-dim" style="width:50px" /> &#215; 
               <input type="text" id="wdp-dialog-height" class="wdp-dialog-dim" style="width:50px" /> pixels</p>
            <p>[width] &#215; [height]</p>
            <div class="wdp-display-options">
                <p><strong>Choose a player display:</strong></p>
                    <input type="radio" id="wdp-theme-player" name="wdp-theme" value="inline-player" checked/><label for="wdp-theme-player"> for Standard Inline</label><br />
                    <input type="radio" id="wdp-theme-grid" name="wdp-theme" value="grid" /><label for="wdp-theme-grid"> Overlay gallery; tiles thumbnails at 180px wide or tall</label><br />
                    <input type="radio" id="wdp-theme-grid-box" name="wdp-theme" value="grid box-thumbs" /><label for="wdp-theme-grid-box"> Gallery with letterbox, pillarbox</label><br />                
                </p>
            </div>
            <div class="wdp-thumbnail-options">
                <p><strong>Thumbnail Options:</strong></p>
                <p><input type="checkbox" id="wdp-hide-thumbs"/><label for="wdp-hide-thumbs"> Expandable thumbnail tray</label><br />
                   <input type="checkbox" id="wdp-disable-thumbs"/><label for="wdp-disable-thumbs"> Disable thumbnails and credits</label><br />
                   <input type="checkbox" id="wdp-auto-slideshow"/><label for="wdp-auto-slideshow"> Slideshow mode (image feeds only)</label></p>
            </div>               
        </div>
    </div>
</div>