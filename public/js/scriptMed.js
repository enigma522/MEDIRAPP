let medecins=[]


$.ajax({
  url: "http://127.0.0.1:8000/mespatients/"+document.getElementById('med_id').value,
  datatype: 'json',
  async: false,
  success: function(data) {
    console.log(data);
      medecins = data;

  },
});

 function afficherMedecins() {
    
    let meds = document.getElementById('group');
    for (var i = 0; i < medecins.length; i++) {
        let medecin = medecins[i];
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
        image.src = "https://previews.123rf.com/images/indomercy/indomercy1501/indomercy150100019/35500150-doctor-cartoon-illustration.jpg";
        image.className = 'card-img-top w-100'
        image.style="width: 50%; height: auto; "
        let c = document.createElement('div');
        c.className = "card-body w-100";
        let nom = document.createElement('h5');
        nom.className = "card-title";
        nom.innerHTML = medecin.lastname;
        let detail = document.createElement('p');
        detail.className = "card-text";
        detail.innerHTML = 'Age: ' + medecin.age + '<br>' + ' Specialté: ' + medecin.specialite  ;
        let footer = document.createElement('div');
        footer.className = 'card-footer';
        let footerText = document.createElement('small');
        footerText.className = 'text-muted';
        footerText.innerHTML = 'Dernière consultation: ' + medecin.lastupdated;
        footer.appendChild(footerText);

        /// link by masmoudi
        var a = document.createElement('a');
        a.className = 'button';
        var linkText = document.createTextNode("Chat");
        a.appendChild(linkText);
        a.title = "chat";
        a.href = "/chatwith/"+medecin.id;
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

afficherMedecins();