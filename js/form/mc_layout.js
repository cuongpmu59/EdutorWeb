document.getElementById('mc_view_list').addEventListener('click', () => {
  const wrapper = document.getElementById('mcTableWrapper');
  if (wrapper.style.display === 'none' || wrapper.style.display === '') {
    wrapper.style.display = 'block';
  } else {
    wrapper.style.display = 'none';
  }
});
