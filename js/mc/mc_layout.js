document.getElementById('mc_view_list').addEventListener('click', () => {
  const wrapper = document.getElementById('mcTableWrapper');
  wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
    ? 'block'
    : 'none';
});
