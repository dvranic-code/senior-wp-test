window.addEventListener('load', function () {

  var votingContainer = document.getElementById('voting-container');

  // check if voting container exists
  if (votingContainer) {
    var postId = votingContainer.dataset.postId;
    var buttons = document.querySelectorAll('#voting-container .vote-button');

    // check if buttons exist
    if (buttons) {
      buttons = Array.prototype.slice.call(buttons);
      buttons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          vote(e.target);
        });
      });
    }
  }

  function vote(btn) {
    var buttonType = btn.dataset.vote;

    // prepare data to send
    var data = new FormData();
    data.append('action', 'manage_voting');
    data.append('_nonce', simplevoting_ajax_obj.nonce);
    data.append('button_type', buttonType);
    data.append('post_id', postId);

    // send data
    fetch(simplevoting_ajax_obj.ajax_url, {
      method: 'POST',
      body: data
    })
    .then(function (response) {
      return response.text();
    })
    .then(function (data) {
      votingContainer.innerHTML = data;
    });
  }
});
