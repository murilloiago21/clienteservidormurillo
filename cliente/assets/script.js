var meutoken = '-1';
var reg = '-1';
var statuslogin = false;
var conexao = '-1';

var el = document.getElementById('btn-login');
if (el != null) {
  el.addEventListener("click", function () { enviarLogin() });
}
el = document.getElementById('sessao');
if (el != null) {
  el.addEventListener("click", function () { callsessao() });
}

function callsessao() {
  if (reg != '-1') {
    document.getElementById('regsessao').value = reg;
    document.getElementById('endip').value = conexao;
    document.getElementById('token').value = meutoken;
    if (statuslogin == true) {
      return true;
    } else {
      return false;
    }
  }
}

function enviarLogin() {
  var ip = document.getElementById('enderecoip').value;
  var porta = document.getElementById('enderecoporta').value;
  var endereco = 'http://' + ip + ':' + porta + '/login';
  var connect = 'http://' + ip + ':' + porta + '/';
  var registro = document.getElementById('registro').value;
  var senha = document.getElementById('senha').value;
  var hash = CryptoJS.MD5(senha);
  var status;
  if (ip.length <= 0 || ip.length <= 0) {
    alert('Preencha Endereço IP e PORTA!');
  } else if (registro.length <= 0 && senha.length <= 0) {
    alert('Preencha os campos!');
  } else {
    const data = {
      registro: parseInt(registro),
      senha: hash.toString(),
    };
    fetch(endereco, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
      .then(response => {
        status = response.status;
        if (status === 200) {
          statuslogin = true;
        }
        if (response.status === 200) {
          return response.json();
        } else if (response.status === 401) {
          return response.json();
        } else {
          throw new Error('Erro na requisição');
        }
      })
      .then(data => {
        console.log(data);
        alert(status + ' - \n' + JSON.stringify(data));
        if (status === 200) {
          meutoken = data['token'];
          reg = registro;
          conexao = connect;
          document.getElementById('sessao').click();
        }
      })
      .catch(error => {
        console.error('Ocorreu um erro na requisição:', error);
      });
  }
}

function enviarLogout(apiUrl, gettoken) {
  var status;
  var endereco = apiUrl + 'logout';
  const data = {

  };
  fetch(endereco, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ` + gettoken
    },
    body: JSON.stringify(data),
  })
    .then(response => {
      status = response.status;
      if (response.status === 200) {
        return response.json();
      } else if (response.status === 401) {
        return response.json();
      } else {
        throw new Error('Erro na requisição');
      }
    })
    .then(data => {
      console.log(data);
      alert(status + ' - \n' + JSON.stringify(data));
      if (status === 200) {
        document.getElementById('sairsessao').click();
      }
    })
    .catch(error => {
      console.error('Ocorreu um erro na requisição:', error);
    });
}

function acaoHome(valor) {
  if (valor == 'cadastro' || valor == 'leitura') {
    if (valor == 'leitura') {
      //aqui
    } else {
      document.getElementById('acao').value = valor;
      document.getElementById('enviaracao').click()
    }
  }
}

function voltarHome() {
  window.location.replace('../index.php');
}

function enviarCadastro(apiUrl, gettoken) {
  var status;
  var endereco = apiUrl + 'usuarios';
  var registrocad = document.getElementById('registrocad').value;
  var nomecad = document.getElementById('nomecad').value;
  var emailcad = document.getElementById('emailcad').value;
  var senhacad = document.getElementById('senhacad').value;
  var tipousuariocad = null;
  if (document.getElementById('tipousuariocad0').checked) {
    tipousuariocad = 0;
  } else if (document.getElementById('tipousuariocad1').checked) {
    tipousuariocad = 1;
  } else {
    alert('Preencha o tipo de usuário')
  }
  if (/^\d+$/.test(registrocad)) {
    if (tipousuariocad != null) {
      var hash = CryptoJS.MD5(senhacad);
      const data = {
        registro: parseInt(registrocad),
        nome: nomecad,
        email: emailcad,
        senha: hash.toString(),
        tipo_usuario: tipousuariocad
      };
      fetch(endereco, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ` + gettoken
        },
        body: JSON.stringify(data),
      })
        .then(response => {
          status = response.status;
          if (response.status === 200) {
            return response.json();
          } else if (response.status === 401) {
            return response.json();
          } else if (response.status === 403) {
            return response.json();
          } else {
            throw new Error('Erro na requisição');
          }
        })
        .then(data => {
          console.log(data);
          alert(status + ' - \n' + JSON.stringify(data));
          if (status === 200) {
            voltarHome();
          }
        })
        .catch(error => {
          console.error('Ocorreu um erro na requisição:', error);
        });
    }
  }else {
    alert('O registro não pode conter letras!');
  }
}

function lerUsuarios(apiUrl, gettoken) {
  var endereco = apiUrl + 'usuarios';
  var status;
  fetch(endereco, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ` + gettoken
    },
  })
    .then(response => {
      status = response.status;
      if (response.status === 200) {
        return response.json();
      } else if (response.status === 401) {
        return response.json();
      } else if (response.status === 403) {
        return response.json();
      } else {
        throw new Error('Erro na requisição');
      }
    })
    .then(data => {
      console.log(data);
      alert(status + ' - \n' + JSON.stringify(data));
      if (status === 200) {
      }
    })
    .catch(error => {
      console.error('Ocorreu um erro na requisição:', error);
    });

}



//EXEMPLOS PUT GET POST DELETE
/*GET

// URL da sua API PHP
const apiUrl = 'https://exemplo.com/sua-api.php';

// Fazer uma requisição GET
fetch(apiUrl)
  .then(response => {
    // Verificar se a resposta da API é bem-sucedida (status 200)
    if (response.status === 200) {
      // Transformar a resposta em JSON
      return response.json();
    } else {
      throw new Error('Erro na requisição');
    }
  })
  .then(data => {
    // O objeto 'data' contém os dados JSON da resposta
    console.log(data);
  })
  .catch(error => {
    console.error('Ocorreu um erro na requisição:', error);
  });
  
  */

/* POST  
const apiUrl = 'https://exemplo.com/sua-api.php';

const data = {
  nome: 'João',
  email: 'joao@example.com',
};

fetch(apiUrl, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(data),
})
  .then(response => {
    if (response.status === 200) {
      return response.json();
    } else {
      throw new Error('Erro na requisição');
    }
  })
  .then(data => {
    console.log(data);
  })
  .catch(error => {
    console.error('Ocorreu um erro na requisição:', error);
  });




*/


/*PUT

const apiUrl = 'https://exemplo.com/sua-api.php';
const resourceId = 123; // Substitua pelo ID ou identificador do recurso que você deseja atualizar

const data = {
  nome: 'João Atualizado',
  email: 'joao@atualizado.com',
};

fetch(`${apiUrl}/${resourceId}`, {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(data),
})
  .then(response => {
    if (response.status === 200) {
      return response.json();
    } else {
      throw new Error('Erro na requisição');
    }
  })
  .then(data => {
    console.log(data);
  })
  .catch(error => {
    console.error('Ocorreu um erro na requisição:', error);
  });
  
  */
/* DELETE

const apiUrl = 'https://exemplo.com/sua-api.php';
const resourceId = 123; // Substitua pelo ID ou identificador do recurso que você deseja excluir

fetch(`${apiUrl}/${resourceId}`, {
  method: 'DELETE',
})
  .then(response => {
    if (response.status === 204) {
      // Status 204 significa "No Content" e indica que a exclusão foi bem-sucedida
      console.log('Recurso excluído com sucesso.');
    } else {
      throw new Error('Erro na requisição');
    }
  })
  .catch(error => {
    console.error('Ocorreu um erro na requisição:', error);
  });
  */
