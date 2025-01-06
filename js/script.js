document.getElementById('recommend-btn').addEventListener('click', function() {
    alert('추천받기 버튼을 클릭했습니다!');
});

const heartButtons = document.querySelectorAll('.heart-btn');

heartButtons.forEach(button => {
    button.addEventListener('click', function() {
        if (this.textContent === '♡') {
            this.textContent = '❤️';
        } else {
            this.textContent = '♡';
        }
    });
});
