class Player {
    player;
    intervalId;
    paused;
    nodeList;
    constructor(player, nodeList) {
        this.timeSlider = $("#time");
        this.volumeSlider = $("#volume-slider");
        this.thumbnail = $("#thumbnail");
        this.elapsedTime = $("#time-elapsed");
        this.songLength = $("#song-length");
        this.pauseButton = $("#play");
        this.title = $("#title");
        this.artist = $("#artist");
        this.nextButton = $("#next-song");
        this.prevButton = $("#prev-song");

        this.player = player;
        this.nodeList = nodeList;
        this.player.addEventListener("onStateChange", (e) => {
            if (e.data === YT.PlayerState.PLAYING) {
                this.intervalId = window.setInterval(()=>this.updatePlayerTime(), 1000);
            }
            else clearInterval(this.intervalId);
            if(e.data === 0){
                if(!this.nodeList.hasNext()){
                    return;
                }
                const nextSong = nodeList.next();
                this.loadVideo(nextSong.videoId);
            }
            else if(e.data === YT.PlayerState.UNSTARTED){
                if (this.nodeList.currentNode !== null){
                    this.setVideo();
                    this.intervalId = window.setInterval(()=>this.updatePlayerTime(), 1000);
                }
            }
        });

        this.pauseButton.on("click", ()=> {
            this.player.getPlayerState() === 2 ? this.play() : this.pause();
        });

        this.volumeSlider.on("input", ()=> {
            this.player.setVolume(this.volumeSlider.val());
        });

        this.nextButton.on("click", () => {
            const nextSong = nodeList.next();
            this.loadVideo(nextSong.videoId);
        });
        this.prevButton.on("click", () => {
            const nextSong = nodeList.prev();
            if (nextSong === false) return;
            this.loadVideo(nextSong.videoId);
        });

        this.timeSlider.on("input", () => {
            const timePercent = parseInt(this.timeSlider.val())/100;
            this.updatePlayerTimeElapsed(timePercent*this.player.getDuration());
        });
        this.timeSlider.on("change", () => {
            const timePercent = parseInt(this.timeSlider.val())/100;
            this.player.seekTo(this.player.getDuration()*timePercent, true);
        });
    }

    pause(){
        this.player.pauseVideo();
        this.pauseButton.removeClass("playing");
        this.pauseButton.addClass("paused");
    }

    play(){
        this.player.playVideo();
        this.pauseButton.removeClass("paused");
        this.pauseButton.addClass("playing");
    }

    updatePlayerTimeElapsed(time){
        this.elapsedTime.text(`${Math.floor(time/60)}:${Math.floor(time)%60 >= 10 ? Math.floor(time)%60 : `0${Math.floor(time)%60}`}`);
    }

    updatePlayerTime(){
        this.updatePlayerTimeElapsed(this.player.getCurrentTime());
        this.timeSlider.val(100*(this.player.getCurrentTime()/this.player.getDuration()));
    }

    loadVideo(videoId){
        this.player.loadVideoById(videoId, 0);
        this.thumbnail.attr('src', `https://img.youtube.com/vi/${videoId}/sddefault.jpg`);
    }

    setVideo(){
        let time = this.player.getDuration()
        this.songLength.text(`${Math.floor(time / 60)}:${Math.floor(time) % 60 >= 10 ? Math.floor(time) % 60 : `0${Math.floor(time) % 60}`}`);
        this.elapsedTime.text(`0:00`);
        this.timeSlider.val(0);
        this.title.text(this.player.getVideoData().title);
        this.artist.text(this.player.getVideoData().author);
        this.pauseButton.prop('disabled', false);
        if (nodeList.hasNext()) this.nextButton.prop('disabled', false);
        else this.nextButton.prop('disabled', true);

        if (nodeList.hasPrev()) this.prevButton.prop('disabled', false);
        else this.prevButton.prop('disabled', true);
    }

    clearVideo(){
        this.player.stopVideo();
        clearInterval(this.intervalId);
        this.thumbnail.attr('src', "images/gradient.png");
        this.elapsedTime.text(`-:--`);
        this.songLength.text("-:--");
        this.timeSlider.val(0);
        this.title.text("--");
        this.artist.text("--");
        this.pauseButton.prop('disabled', true);
        this.nextButton.prop('disabled', true);
        this.prevButton.prop('disabled', true);
    }
}