class Connection {
    line; end; neighbor;
    constructor(line, neighbor, end) {
        this.line = line;
        this.end = end;
        this.neighbor = neighbor;
    }

    move(x, y){
        if (this.end === 0){
            this.line.updateLine(x, y, 0, 0);
        }else{
            this.line.updateLine(0, 0, x, y);
        }
    }
}

class Line {
    x1; y1; x2; y2; x; y; ele;
    constructor(x1, y1, x2, y2) {
        this.ele = $("<div class='connection'></div>");
        $("#lines").append(this.ele);
        this.x1 = 0; this.y1 = 0; this.x2 = 0; this.y2 = 0;
        this.updateLine(x1+50, y1+50, x2+50, y2+50);
    }

    updateLine(offsetX1, offsetY1, offsetX2, offsetY2) {
        this.x1 += offsetX1; this.y1+= offsetY1; this.x2+= offsetX2; this.y2+= offsetY2;
        const a = this.x1 - this.x2,
            b = this.y1 - this.y2,
            c = Math.sqrt(a * a + b * b);
        const sx = (this.x1 + this.x2) / 2;
        this.y = (this.y1 + this.y2) / 2;
        this.x = sx - c / 2;
        const alpha = Math.PI - Math.atan2(-b, a);
        this.ele.css({'top': `${this.y}px`, 'left': `${this.x}px`, 'width': `${c}px`,
            'transform': `rotate(${alpha}rad)`});
    }

    shiftLine(offsetX, offsetY) {
        this.x1 += offsetX; this.y1+= offsetY; this.x2+= offsetX; this.y2+= offsetY;
        this.y += offsetY; this.x += offsetX;
        this.ele.css({'top': `${this.y}px`, 'left': `${this.x}px`});
    }
}

class Node {
    neighbors; videoId; y; x; ele; id;
    constructor(videoId, x, y, id) {
        this.id = id;
        this.neighbors = [];
        this.videoId = videoId;
        this.x = x;
        this.y = y;
        this.ele = $("<div class='node'></div>");
        this.ele.css({'left': `${this.x}px`, 'top': `${this.y}px`});
        this.ele.append(`<img draggable='false' src='https://img.youtube.com/vi/${this.videoId}/sddefault.jpg' alt=''>`);
        $(this.ele).on("click", (e)=> {
            e.stopPropagation();
        });
        $(this.ele).on("mousedown", (e)=> {
            e.stopPropagation();
            nodeList.startDragSelection(e, this);
        });
        this.ele.on("dblclick", (e) => {
            e.stopPropagation();
            nodeList.playNode(e, this);
        });
        $("#nodes").append(this.ele);
    }

    delete(){
        this.neighbors.forEach((con) => {
            this.unLink(con.neighbor);
        });
        nodeList.nodes = nodeList.nodes.filter((node) => node !== this);
        this.ele.remove();
        $.post(window.location.href+"&action=delete_node", {node_id: this.id});
        nodeList.clearPath();
    }

    move(dx, dy){
        this.x += dx;
        this.y += dy;
        this.ele.css({'left': `${this.x}px`, 'top': `${this.y}px`});
        this.neighbors.forEach((con) => {
            if (con.neighbor.selected()){
                if(con.end === 0) con.line.shiftLine(dx, dy);
            }else con.move(dx, dy);
        });
    }

    link(other){
        if (other === this) return;
        if (this.neighbors.some((con) => con.neighbor === other)) return;
        const line = new Line(this.x, this.y, other.x, other.y);
        this.neighbors.push(new Connection(line, other, 0));
        other.neighbors.push(new Connection(line, this, 1));
        $.post(window.location.href+"&action=link_nodes", {node_1: this.id, node_2: other.id});
    }

    unLink(other){
        this.neighbors = this.neighbors.filter((con) => {
            if (con.neighbor === other){
                con.line.ele.remove();
                return false;
            }else return true;
        });
        other.neighbors = other.neighbors.filter((con) => con.neighbor !== this);
        $.post(window.location.href+"&action=unlink_nodes", {node_1: this.id, node_2: other.id});
        nodeList.clearPath();
    }

    select(){
        this.ele.addClass("selected");
    }

    selected(){
        return this.ele.hasClass("selected");
    }

    deselect(){
        this.ele.removeClass("selected");
    }
}

//TODO: check if on delete/unlink, path invalidated
//TODO: highlight play path
//TODO: highlight lines on playpath creation
//TODO: add to playpath on enter if possible

class NodeList {
    nodes;
    selected;
    currentNode;
    playPath;
    playIndex;
    constructor() {
        this.nodes = [];
        this.selected = [];
        this.playPath = [];
        this.playIndex = 0;
        this.currentNode = null;
        const nodeArea = $(".node-area");
        nodeArea.on("click", ()=>this.deselectAll());
        nodeArea.on("dblclick", ()=>this.clearPath());
    }

    get length(){
        return this.nodes.length;
    }

    addNode(node){
        this.nodes.push(node);
    }

    playNode(e, node){
        if (this.playPath.indexOf(node) === -1){
            this.clearPath();
            this.playPath[0] = node;
        }
        this.setCurrent(node);
        player.loadVideo(node.videoId);
        player.play();
    }

    deselectAll(){
        this.selected = [];
        $(".selected").removeClass("selected");
    }

    modifySelection(node, additive){
        if(additive){
            if (node.selected()){
                node.deselect();
                this.selected = this.selected.filter((nde)=> nde!== node);
            }else{
                node.select();
                this.selected.push(node);
            }
        }else{
            if (node.selected()) return;
            this.deselectAll()
            node.select();
            this.selected.push(node);
        }
    }

    startDragSelection(e, node){
        e.stopPropagation();
        this.modifySelection(node, e.shiftKey);
        document.onmousemove = (e) => this.dragSelection(e);
        document.onmouseup = () => {
            document.onmouseup = null;
            document.onmousemove = null;
            $.post(window.location.href+"&action=move_node", {x: node.x, y: node.y, node_id: node.id});
        };
    }

    dragSelection(e) {
        const x = e.movementX;
        const y = e.movementY;
        this.selected.forEach((node) => node.move(x,y));
    }

    deleteSelected(){
        this.selected.forEach((node) => node.delete());
        this.clearPath();
    }

    clearPath(){
        $(".path").removeClass("path");
        this.playPath = [];
        this.playIndex = 0;
        this.setCurrent(null);
        player.clearVideo();
    }

    selectedToPath(){
        this.clearPath();
        const tempPath = [];
        this.selected.forEach((node)=>tempPath.push(node));
        if (this.validatePath(tempPath)) {
            this.playPath = tempPath;
            this.setCurrent(this.playPath[0]);
            this.playIndex = 0;
        }
        for(let i = 0; i < this.playPath.length - 1; i++){
            this.playPath[i].neighbors.some((con) => {
                if (con.neighbor === this.playPath[i+1]){
                    con.line.ele.addClass("path");
                }
            })
        }
    }

    validatePath(path){
        for(let i = 0; i < path.length - 1; i++){
            if (!path[i].neighbors.some((con) => con.neighbor === path[i+1])) return false;
        }
        return true;
    }

    next(){
        if (this.hasNext()){
            //if needed, randomly add playpath
            if (this.playPath.length === this.playIndex + 1){
                let node = this.currentNode.neighbors[Math.floor(Math.random()*this.currentNode.neighbors.length)].neighbor;
                while (node === this.playPath[this.playIndex - 1]){
                    node = this.currentNode.neighbors[Math.floor(Math.random()*this.currentNode.neighbors.length)].neighbor;
                }
                this.playPath.push(node);
            }
            //increment current
            this.playIndex ++;
            this.setCurrent(this.playPath[this.playIndex]);
            return this.currentNode;
        }
        return false;
    }

    prev(){
        if (this.hasPrev()){
            this.playIndex--;
            this.setCurrent(this.playPath[this.playIndex]);
            return this.currentNode;
        }
        return false;
    }

    setCurrent(node){
        $(".current").removeClass("current");
        if (node !== null) {
            node.ele.addClass("current");
        }
        this.currentNode = node;
    }

    hasPrev(){
        return this.playIndex > 0;
    }

    hasNext(){
        //no current node
        if (this.playPath.length === 0 || this.currentNode === null){
            return false;
        }
        //no neighboring options
        if (this.currentNode.neighbors.length === 0){
            return false
        }
        //no option for random next
        if (this.playPath.length === this.playIndex + 1){
            if (this.currentNode.neighbors.length === 1){
                if (this.playPath[this.playIndex - 1] === this.currentNode.neighbors[0].neighbor){
                    return false
                }
            }
        }
        return true;
    }

    loadFromJson(json){
        const arr = [];
        json.nodes.forEach((node) => {
            const newNode = new Node(node.yt_id, parseInt(node.x), parseInt(node.y), node.id);
            this.addNode(newNode);
            arr.push(newNode);
        });
        json.connections.forEach((con) => {
            const node1 = arr.filter((node) => node.id === con.node_1)[0];
            const node2 = arr.filter((node) => node.id === con.node_2)[0];
            node1.link(node2);
        });
    }
}