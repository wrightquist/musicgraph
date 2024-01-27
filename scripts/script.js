let nodeList;
let player;

//TODO: save/load
//TODO: fix modals and buttons
//TODO: fix selection styles
//TODO: change playpause for mobile
//TODO: remove share button

$(() => {
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    $("#playlist-name").on("change", function () {
        $.post(window.location.href+"&action=change_title", {title: $(this).val()});
    })

    nodeList = new NodeList();
    $.getJSON(window.location.href, function (json) {
        nodeList.loadFromJson(json);
    });

    $("#visibility").on("click", (e)=>{
        const checked =  $("#visibility")[0].checked?"false":"true";
        $.post(window.location.href+"&action=change_visibility", {public: checked});
    });

    $("#fork-button").on("click", (e)=>{
        $.post(window.location.href+"&action=fork").done((data)=>{
            location = data.redirect;
        });
    });

    $("#like-button").on("click", (e)=>{
        if ($("#like-button").hasClass("liked")){
            $.post(window.location.href+"&action=unlike");
            $("#like-button").removeClass("liked");
        }else{
            $.post(window.location.href+"&action=like");
            $("#like-button").addClass("liked");
        }
    });

    $("#info").on("click", (e)=>{
        e.stopPropagation();
        $("#shortcuts_outer").show()
    });
    $("#shortcuts").on("click", (e)=>e.stopPropagation());
    $(document).on("click", ()=>$("#shortcuts_outer").hide())

    const newNodeButton = $("#new-node-button");
    const newNodeCancel = $("#new-node-cancel");
    const newNodeError = $("#new-node-error");
    const songUrl = $("#song-url");
    const newNodeModal = $("#new-node-modal");
    const createNode = $("#create-node");



    newNodeButton.on("click", (e) => {
        e.stopPropagation();
        const videoId = getVideoIdFromUrl(songUrl.val());
        if (videoId === false){
            newNodeError.show();
        }else{
            newNodeError.hide();
            //TODO: move to center and mousedown
            const newNode = new Node(videoId, 4, 5, null);
            nodeList.addNode(newNode);
            $.post(window.location.href+"&action=create_node", {x: newNode.x, y: newNode.y, yt_id: newNode.videoId}).done((data)=>{
                if (data.success){
                    newNode.id = data.id;
                }
            });
            newNodeModal.hide();
        }
    })

    createNode.on("click", () => {
        newNodeError.hide();
        songUrl.val("");
        newNodeModal.show();
        songUrl.focus();
    });

    newNodeModal.on("click", (e) => {
        if ( e.target.id === 'new-node-modal' ) {
            newNodeModal.hide();
        }
    })

    $(document).on("keyup", (e) => {
        //l+shift
        if(e.which === 76 && e.shiftKey){
            if (nodeList.selected.length === 2) {
                nodeList.selected[0].link(nodeList.selected[1]);
            }
        }
        //u+shift
        else if(e.which === 85 && e.shiftKey){
            if (nodeList.selected.length === 2) {
                nodeList.selected[0].unLink(nodeList.selected[1]);
            }
        }
        //delete or backspace
        else if(e.which === 8 || e.which === 46){
            nodeList.deleteSelected();
        }
        //enter
        else if (e.which === 13){
            if (nodeList.selected.length >= 2) {
                nodeList.selectedToPath();
            }
            nodeList.setCurrent(nodeList.playPath[0]);
            nodeList.playNode(null, nodeList.playPath[0]);
        }
    });

    newNodeCancel.on("click", (e) => {
        newNodeModal.hide();
    })
});


let ytPlayer;

function onYouTubeIframeAPIReady() {
    ytPlayer = new YT.Player('player', {
        height: '0',
        width: '0',
        playerVars: {
            'playsinline': 1,
            'controls': 0,
            'disablekb': 1,
            'fs': 0,
            'enablejsapi': 1,
            'modestbranding': 1
        },
        events: {'onReady': onPlayerReady}
    });
}

function onPlayerReady() {
    player = new Player(ytPlayer, nodeList);
}

function getVideoIdFromUrl(url) {
    const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[7].length === 11) ? match[7] : false;
}

