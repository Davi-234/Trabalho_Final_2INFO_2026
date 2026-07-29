function apagar_elemento(element_id) {
  const element = document.getElementById(element_id);
  if (element === null) {
    return;
  }
  let parent_element = element.parentNode;
  parent_element.removeChild(element);
}
