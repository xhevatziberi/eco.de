document.addEventListener("DOMContentLoaded", function() {
    selectFirstFilterButton();
});

let selectFirstFilterButton = () => {
    let inite = 0;
    setInterval(function(){
        if (document.readyState == "complete" && inite == 0 )  {
            let _id = document.querySelector('#tiles-filter-home');
            if (_id) {
                let _first = _id.querySelector('button.e-filter-item');
                _first.click();
                inite++;
            }
        }
    }, 100);
}