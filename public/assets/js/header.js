const body = document.querySelector('body');    // GET body
const burger = document.querySelector('#burger'); // GET logo buger 
const notifBtn = document.querySelector('#notif'); // GET the notif button
const notifBox = document.querySelector('#notificationBox');    // GET the notification box
const navbarResponsive = document.querySelector('#navResponsive'); //navbar content
var countBurger = 0;  // count for open and close navbar content
var countNotif = 0;   // count for open and close notification content


/**
 * Description :
 * create a loop of open & close navbar content
 */
burger.addEventListener('click', ()=>{

    countBurger  +=1 // count +=1
    // if count = 1 then open navbar content
    countBurger == 1  ? (navbarResponsive.style.display = 'block', notifBox.style.display = 'none') : null;
    // if count = 2 then close navbar content and count = 0
    countBurger == 2  ? (navbarResponsive.style.display = 'none', countBurger = 0) : null;
});

/**
 * Description :
 * create a loop of open & close notification content
 */
notifBtn.addEventListener('click', (event)=>{

    event.stopPropagation(); // Empêche la propagation de l'événement au body
    countNotif  +=1 // count +=1
    //if count = 1 then open notification content
    countNotif == 1  ? (notifBox.style.display = 'flex') : null;
    //if count = 2 then close notification content and count = 0
    countNotif == 2  ? (notifBox.style.display = 'none', countNotif = 0) : null;
});

// when clicked body then close nav bar et count = 0
body.addEventListener('click', ()=> (notifBox.style.display = 'none' ,countNotif = 0));

// property click
notifBox.addEventListener('click',(event)=> event.stopPropagation());

// FETCH the route of group/invitation from group controller
fetch('/group/invitation')
.then(response => response.json())
.then(data=>{
    notifBox.innerHTML += `<h1><span>${data.notifications.length}</span> notifications</h1>`;

    for (let i = 0; i < data.notifications.length; i++) {
        notifBox.innerHTML += `
            <article>
                <h2>${data.notifications[i].authors} invited you to dinner</h2>
                <p>you want to go and eat with these people ?</p>
                <div class='dataBox'>
                    <div class='first'>
                        <h4>${data.notifications[i].createGroupDate.substr(11,8)}</h4>
                        <span>${data.notifications[i].createGroupDate.substr(0,10)}</span>
                    </div>
                    <div class='second'>
                        <h4>Who eats ?</h4>
                        <span>${data.notifications[i].guests.length} people</span>
                    </div>
                    <div class='third'>

                    </div>
                </div>
                <div class='btnBox'>
                    <a href='/group/response/${data.notifications[i].id}/false'><button class='btnNotif'>Rejects</button></a>
                    <a href='/group/response/${data.notifications[i].id}/true'><button  class='btnNotif'>Accept</button></a>
                </div>
            </article>
            `;
            
        const thirdDiv = document.querySelectorAll('.third')[i];
        data.notifications[i].guests.forEach(guest => {
            const img = document.createElement('img');
            img.src = guest.guest.image;
            img.alt = 'img';
            thirdDiv.appendChild(img);
        });
    }
})
