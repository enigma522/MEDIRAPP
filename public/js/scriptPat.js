
let patients=[]


$.ajax({
  url: "http://127.0.0.1:8000/mesMedecins/"+document.getElementById('pat_id').value,
  datatype: 'json',
  async: false,
  success: function(data) {
    console.log(data);
      patients = data;

  },
});




 function afficherPatients() {
    let meds = document.getElementById('group');
    for (var i = 0; i < patients.length; i++) {
        let patient = patients[i];
        let card = document.createElement('div');
        card.className = 'card mb-3';
        card.style.transition = 'transform 1s ease-in-out'; 
       

        let divb= document.createElement('div');
        divb.className="col-md-4 text-right ml-auto";
        let button=document.createElement('button');
        button.className = 'btn btn-primary ml-auto';   
        button.innerHTML = 'Chat';
        

        //card.style='style="width: 80%; height: 80%;'
        let image = document.createElement('img');
        image.src = "https://media.istockphoto.com/id/1286989824/video/sick-person-animation-of-cough-and-runny-nose-cartoon.jpg?s=640x640&k=20&c=AmLjWcm3EIKL46RZ3jLuSuhD-4ROTU1xSQMSGEPqSnI=";
        image.className = 'card-img-top w-100'
        image.style="width: 50%; height: auto; "
        let c = document.createElement('div');
        c.className = "card-body w-100";
        let nom = document.createElement('h5');
        nom.className = "card-title";
        nom.innerHTML = patient.lastname;
        let detail = document.createElement('p');
        detail.className = "card-text";
        detail.innerHTML = 'Age: ' + patient.age + '<br>'  ;
        let footer = document.createElement('div');
        footer.className = 'card-footer';
        let footerText = document.createElement('small');
        footerText.className = 'text-muted';
        footerText.innerHTML = 'Dernière consultation: ' + patient.lastupdated;
        footer.appendChild(footerText);

        /// link by masmoudi
        var a = document.createElement('a');
        a.className = 'button';
        var linkText = document.createTextNode("Chat");
        a.appendChild(linkText);
        a.title = "chat";
        a.href = "/chatwith/"+patient.id;
        footer.appendChild(a);
        ///
        c.appendChild(nom);
        c.appendChild(detail);
        footer.appendChild(divb);
        card.appendChild(image);
        card.appendChild(c);
        card.appendChild(footer);
        let column = document.createElement('div');
        column.className = 'column';

        column.appendChild(card);
        let row = document.createElement('div');
        row.className = 'row';
        row.appendChild(column);



        meds.appendChild(column);



    }

}

afficherPatients();