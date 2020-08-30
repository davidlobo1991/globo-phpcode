<script>
const addComission = function (tax)
{
    let target = document.getElementsByClassName(tax)[0];
    const price_change = target.dataset.price;
    target.style["text-decoration"] = null;

    let cart_total = document.getElementById("cart-total");
    let new_total = parseFloat(cart_total.innerText.replace(/,/g, ''));
    new_total = new_total + parseFloat(price_change);
    cart_total.innerText = formatDeciaml(new_total);
    paypalSelectedFlag = true;
};
const substractComission = function (tax)
{
    let target = document.getElementsByClassName(tax)[0];
    const price_change = target.dataset.price;
    target.style["text-decoration"] = "line-through";
    let cart_total = document.getElementById("cart-total");

    let new_total = parseFloat(cart_total.innerText.replace(/,/g, ''));
    new_total = new_total - parseFloat(price_change);
    cart_total.innerText = formatDeciaml(new_total);
    paypalSelectedFlag = false;
};

const toggleComission = function (tax)
{
    let target = document.getElementsByClassName(tax)[0];
    if (target.style["text-decoration"] === "line-through") {
        addComission(tax);
    } else {
        substractComission(tax);
    }
};

const selectingRadios = function (item, index)
{
    item.addEventListener("click", isPaypalSelected);
};

const isPaypalSelected = function (item, index)
{
    let radioPaypal = document.querySelector("input[value='paypal']");
    if (radioPaypal.checked) {
        if (paypalSelectedFlag === false) {
            addComission("TAX-PAYPAL");
        }
    } else {
        if (paypalSelectedFlag === true) {
            substractComission("TAX-PAYPAL");
        }
    }
};

let paypalSelectedFlag = true;

document.addEventListener("DOMContentLoaded", function() {
    let radios = document.querySelectorAll("input[name='payment_option']");
    radios.forEach(selectingRadios);
});

function formatDeciaml(decimal) {
    console.log(decimal);
    let formated = Number(decimal.toFixed(2)).toLocaleString('en');
    console.log(formated);
    let decimalIndex = formated.indexOf('.');
    if (decimalIndex < 0) {
        formated = formated + ".0";
        decimalIndex = formated.indexOf('.');
    }
    console.log(decimalIndex);

    // if (decimalIndex >= 0) {
        formated = (formated + "0").substring(0, decimalIndex + 3);
    // }

    return formated;
}
</script>
