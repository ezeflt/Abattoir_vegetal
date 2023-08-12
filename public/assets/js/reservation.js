// all cards
const reservationCard = document.querySelectorAll('article');
// first data card
const firstCard = document.querySelectorAll('.firstData');
// second data card
const secondCard = document.querySelectorAll('.dataContainer');
// count click card
var count = 0;

/**
 * on clicked each cards, open the secondCard and close firstCard
 * @param card card is each reservation card
 * @index are each card index
 */
reservationCard.forEach((card, index) => {

    card.addEventListener('click', () => {
        count +=1;
        // when count  = 1 then open secondCard and close firstCard
        count == 1 ? (secondCard[index].style.display = 'flex', firstCard[index].style.display = 'none') : null ;
        // when count  = 2 then open secondCard, close firstCard and reset count to 0
        count == 2 ? (firstCard[index].style.display = 'block', secondCard[index].style.display = 'none', count = 0) : null ;
    });

});