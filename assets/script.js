function searchStudent(val) {
    fetch('../ajax/search_students.php?q=' + val)
        .then(res => res.text())
        .then(data => document.getElementById('result').innerHTML = data);
}

function selectStudent(id) {
    window.location.href = '?edit=' + id;
}