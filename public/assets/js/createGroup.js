
// RIEN ICI
document.addEventListener('DOMContentLoaded', (e) => {

  const myCheckboxes = document.querySelectorAll(".filter-checkbox");    // get all input checkboxs
  const filters = [];    //initialise filters array

  /**
   * loop on all input type checkboxs
   */
  myCheckboxes.forEach(checkBox => {
    filters[checkBox.id] = checkBox.checked;             // add all ID checkbox and his values to the filters array

    // when clicked on input
    checkBox.addEventListener('click', (e) => {
      filters[e.currentTarget.id] = e.target.checked;   // add the ID input checked with his value to the filters array
      refreshUsersSuggestion();                         // to use the refreshUsersSuggestion function
    });
  });


  // if keys's checkbox array selected > 0
  if (Object.keys(filters).length > 0) {
    refreshUsersSuggestion(); // refresh the user list
  }


  /**
   * step 1 -> add filter data checkbox to the url 
   * step 2 -> fetch url
   * step 3 -> add user to the group
   * step 4 -> create a group reservation
   */
  function refreshUsersSuggestion() {

    // STEP 1
    let url = '/group/users-list/';

    let filtersStr = '';    // initialise the filter string

    // loop on the keys from filters array
    Object.keys(filters).forEach(elName => {
      if (filters[elName]) {
        let filter = document.getElementById(elName).dataset['filter']; // get all data from filter
        filtersStr += (filtersStr == '') ? filter : '#' + filter;       // add the filter data to the filtersStr
      }
    });

    url += filtersStr; // concatenates url + filtersStr

    // STEP 2
    let idUsers = []; // initialise the ID users selected array

    fetch(url)                                // fetch ('/group/users-list/')
      .then(response => response.text())     // return the response in text format
      // rename response to html
      .then(html => {
          document.querySelector('#allUsers').innerHTML = html;    // get container users and add html data

          // STEP 3
          let idUser = document.querySelectorAll('#idUser');      //get id's user from button Gimme this friend
          const datePicker = document.querySelector('#datePicker');     // get input type date

          // create a loop on every click event
          idUser.forEach(function (element) {
            // when clicked on button
            element.addEventListener("click", function(event) {

              let btnClick = event.currentTarget;   // get all value of the button clicked
              let clickedId = btnClick.value;      // get the value of the user ID from the button clicked

              // Toggle the ID in the idUsers array
              if (idUsers.includes(clickedId)) {
                idUsers = idUsers.filter(id => id !== clickedId); // Remove the ID if it's already present
                // update css of element clicked
                btnClick.parentElement.parentElement.parentElement.style.boxShadow = 'rgba(100, 100, 111, 0.2) 0px 7px 29px 0px'; 
                btnClick.style.background = '#fff';
                btnClick.style.color = '#000';
                btnClick.style.border = '1px solid #000';
                // if idUsers.length <= 0 then input date is unvisible and unclickable
                idUsers.length <= 0 && (datePicker.style.opacity = 0.5, datePicker.style.pointerEvents = 'none');
                datePicker.value = null;
                btnAddGroup.style.opacity = 0.5;
                btnAddGroup.style.pointerEvents = 'none';
              } else {
                // Add the ID if it's not already present
                idUsers.push(clickedId);
                // update css of element clicked
                btnClick.parentElement.parentElement.parentElement.style.boxShadow = '#46D4B3 0px 7px 29px 0px';
                btnClick.style.background = '#46D4B3';
                btnClick.style.color = '#fff';
                btnClick.style.border = '1px solid #fff';
                // if idUsers.length > 0 then input date is visible and clickable
                idUsers.length > 0 && (datePicker.style.opacity = 1, datePicker.style.pointerEvents = 'initial');
              }

              // check idUsers array
              console.log(idUsers);
          });
        });



      // refreshUsersSuggestion function END
      // const datePicker = document.querySelector('#datePicker');     // get input type date
      const btnAddGroup = document.querySelector('#addGroup');
      // Fonction pour vérifier la valeur du datePicker
      function checkDatePickerValue() {
        if (datePicker.value) {
          console.log('Date sélectionnée:', datePicker.value);
          btnAddGroup.style.opacity = 1;
          btnAddGroup.style.pointerEvents = 'initial';
          btnAddGroup.style.cursor = 'pointer';
        } else {
          console.log('Aucune date sélectionnée.');
          btnAddGroup.style.opacity = 0.5;
          btnAddGroup.style.pointerEvents = 'none';
        }
      }

      // Vérifier la valeur du datePicker au chargement de la page
      checkDatePickerValue();

      // Ajouter un écouteur d'événement "change" pour le datePicker
      datePicker.addEventListener('change', checkDatePickerValue);
        
      // STEP 4

      // const btnAddGroup = document.querySelector('#addGroup');      // get addGroup button

      // on click addGroup button
      btnAddGroup.addEventListener('click',()=>{

        let urlGroup = '/group/add/';      // initialise url for to use group controller
        let groupeUsers = '';             // initilise groupUsers
        idUsers.forEach(id => {
          let idUsername = id;           // get user id selected from idUsers array
          groupeUsers += (groupeUsers == '') ? idUsername : ',' + idUsername; // add the user ID to the groupUsers string
        });
        urlGroup += `${groupeUsers}/${datePicker.value}`;        // add the user ID to the url

        console.log('brrr', urlGroup); // test log url
        window.location.href = urlGroup; // redirects to the /group/add/ route with the user id selected in the url

      }) 
    });
  }
});
