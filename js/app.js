// ============================================
// JOGAÍ — JavaScript do site
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- 1) Escolha de avatar (cadastro) ----
  var campoAvatar = document.getElementById('avatar_id');
  var opcoes = document.querySelectorAll('#form-registro .avatar-op');

  opcoes.forEach(function (btn) {
    btn.addEventListener('click', function () {
      opcoes.forEach(function (o) { o.classList.remove('selecionado'); });
      btn.classList.add('selecionado');
      if (campoAvatar) campoAvatar.value = btn.dataset.id;
    });
  });

  var formRegistro = document.getElementById('form-registro');
  if (formRegistro) {
    formRegistro.addEventListener('submit', function (ev) {
      if (!campoAvatar.value) {
        ev.preventDefault();
        alert('Escolha um avatar para continuar.');
      }
    });
  }

  // ---- 2) Menu lateral no celular ----
  var abrir = document.getElementById('abrir-menu');
  var menu = document.getElementById('menu');
  if (abrir && menu) {
    abrir.addEventListener('click', function () {
      menu.classList.toggle('aberto');
    });
  }

  // ---- 3) Favoritar sem recarregar a página ----
  document.querySelectorAll('.fav').forEach(function (botao) {
    botao.addEventListener('click', function () {
      var dados = new FormData();
      dados.append('jogo_id', botao.dataset.jogo);

      fetch('favoritar.php', { method: 'POST', body: dados })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          botao.classList.toggle('ativo', res.favorito === true);
        })
        .catch(function () { alert('Não consegui salvar o favorito.'); });
    });
  });

  // ---- 4) Busca automática após parar de digitar ----
  var busca = document.querySelector('.busca input[name="busca"]');
  if (busca) {
    var timer;
    busca.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { busca.form.submit(); }, 600);
    });
  }

});
