// list button of navigation 
const ulNavbar = document.querySelector('#ulNavbar');
// button navbar 
const btnNavRespisove = document.querySelector('#burgerBox');
//respo
const navbarResponsive = document.querySelector('#navResponsive');
// count for open and close modal
var count = 0;
// first nav image
const nav1 = document.querySelector('#nav1');
// second nav image
const nav2 = document.querySelector('#nav2');
// third nav image
const nav3 = document.querySelector('#nav3');
//button close modal
const btnCloseModal = document.querySelector('#xMark');
//modal box
const modalBox = document.querySelector('#modalBox');
//visible value of modal
var modal = false;


/**
 * when clicked btnNavRespisove -> count +1
 * if count = 1 then open nav bar 
 * if count = 2 then close nav bar et count = 0
 */
btnNavRespisove.addEventListener('click', ()=>{
    count+=1 
    count == 1  ? (navbarResponsive.style.display = 'block') : null;
    count == 2  ? (navbarResponsive.style.display = 'none', count = 0) : null;
});

// close modal 
btnCloseModal.addEventListener('click',()=>{
    modal = false, 
    modal ? modalBox.style.display = 'flex' : modalBox.style.display = 'none';
});

fetch('/header')
.then(res=>res.json())
.then(data=>{
    
    //when click on first nav, if user is connected then redirect to page else open modal
    nav1.addEventListener('click',()=>{
        !data.user ? (modal = true)  : ( location.href = '/group/select_group') ;
        modal ? modalBox.style.display = 'flex' : modalBox.style.display = 'none';
    });

    //when click on second nav, if user is connected then redirect to page else open modal
    nav2.addEventListener('click',()=>{
        !data.user ? (modal = true)  : ( location.href = '/group/select_group') ;
        modal ? modalBox.style.display = 'flex' : modalBox.style.display = 'none';
    });

    //when click on third nav, if user is connected then redirect to page else open modal
    nav3.addEventListener('click',()=>{
        !data.user ? (modal = true)  : ( location.href = '/group/select_group') ;
        modal ? modalBox.style.display = 'flex' : modalBox.style.display = 'none';
    });

})