var questions = [
    { text: "Süleyman Çakır kaçıncı bölümde hakkın rahmetine kavuşmuştur?", options: ["46", "44", "45", "42"], correctAnswer: "C" },
    { text: "Hayko Cepkin nerede doğmuştur?", options: ["Bursa", "İstanbul", "Ankara", "Çankırı"], correctAnswer: "B" },
    { text: "Türkiyede mareşal ünvanına sahip kaç kişi var?", options: ["3", "2", "4", "8"], correctAnswer: "B" }
];

var index = 0;
var totalPoints = 0;

function nextQuestion() {
    
    var selected = document.querySelector('input[name="secenek"]:checked');
    if (selected) {
        if (selected.value === questions[index].correctAnswer) {
            totalPoints += 10;
        }
    }

    index++;

    if (index >= questions.length) {
        window.location.href = "resultPage.html?points=" + totalPoints;
        return;
    }

    document.getElementById("question-number").innerText = "Soru: " + (index + 1);
    document.getElementById("answer-area").innerText = questions[index].text;

    var optionsHTML = "";
    var options = questions[index].options;

    for (var i = 0; i < options.length; i++) {
        var label = String.fromCharCode(65 + i);
        optionsHTML += "<label><input type='radio' name='secenek' value='" + label + "' /> " + label + ") " + options[i] + "</label><br />";
    }
    document.querySelector(".options").innerHTML = optionsHTML;
}

function showResults() {
    
    var parameter = new URLSearchParams(window.location.search);
    var points = parameter.get('points');
    document.getElementById("total-points").innerText = "Toplam Puan: " + points;
}
