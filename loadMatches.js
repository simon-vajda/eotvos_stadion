const loadBtn = document.querySelector("#loadBtn");
const table = document.querySelector("#matches");

loadBtn.addEventListener("click", async () => {
  const from = table.rows.length - 1;
  const response = await loadMatches(from);

  response.matches.forEach((m) => {
    let row = table.insertRow();
    let home = row.insertCell();
    let away = row.insertCell();
    let result = row.insertCell();
    let date = row.insertCell();

    home.innerHTML = m.home.name;
    away.innerHTML = m.away.name;
    result.innerHTML = m.home.score + " - " + m.away.score;
    date.innerHTML = m.date;
  });

  loadBtn.style.visibility = response.hasMore ? "visible" : "hidden";
});

async function loadMatches(from) {
  const r = await fetch(`loadMatches.php?from=${from}`);
  const json = await r.json();
  return json;
}
