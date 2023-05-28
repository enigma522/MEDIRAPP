receiver_userid = 0;
const url = 'ws://localhost:3001?token='+document.getElementById('user_token').value;
const socket = new WebSocket(url);
 
socket.addEventListener("open", function() {
    console.log("CONNECTED");
})


function addMessage(message,who) {
    const sanitizedMessage = message;
    const messageHTML = '<div class="message '+who+'">'+sanitizedMessage+'</div>';
    li = document.createElement("li");
    li.classList.add("clearfix");
    li.innerHTML = messageHTML;
    const ul=document.getElementById("chat");
    ul.appendChild(li)
}


document.getElementById("sendBtn").addEventListener("click", function(e) {
    e.preventDefault();
    
    const message = {
        type:'private_chat',
        to:receiver_userid,
        from:document.getElementById('user_id').value,
        message: document.getElementById("msg").value
    };
    console.log(message);
    socket.send(JSON.stringify(message));
    addMessage(message.message,"other-message float-right");
});

socket.addEventListener("message", function(e) {
    e.preventDefault();
    console.log("hh");
    console.log(e.data);
    try
    {
        const message = JSON.parse(e.data);
        addMessage(message.message,"my-message");
    }
    catch(e)
    {
        // Catch any errors
    }
})
document.getElementById("sendBtn").disabled = true;


let friends = document.querySelectorAll('.people-list .chat-list li')

document.querySelector('.chat-list').addEventListener("click", function(e) {
    e.preventDefault();
    console.log(e.target);

    friends.forEach(element => {
        element.classList.remove("active");
    });
    e.target.classList.add("active"); 
    receiver_userid = e.target.getAttribute('data-userid');
    from_userid = document.getElementById('user_id').value;
    document.getElementById('is_active_chat').value = 'YES';
    const ul=document.getElementById("chat");
    ul.innerHTML = "";
    document.getElementById("sendBtn").disabled = false;
    console.log(receiver_userid);
    $.ajax({
        url: 'http://localhost:8000/'+receiver_userid,
        datatype: 'json',
        success: function(data) {
            console.log(data);
            for (var i = 0; i < data.length; i++) {
                if (data[i].id!=from_userid){
                    addMessage(data[i].content,"my-message");
                }else{
                    addMessage(data[i].content,"other-message float-right");
                }
            }

        },
    });

    
});