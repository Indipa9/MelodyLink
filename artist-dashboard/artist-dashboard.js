function editAlbum(button) {
  const eventCard = button.closest('.event-card');
  const albumId = eventCard.dataset.albumId;
  console.log(`Editing album with ID: ${albumId}`);
  // Here you can add more code to handle the editing process, such as opening a modal with the album's current details.
}

function deleteAlbum(button) {
  const eventCard = button.closest('.event-card');
  const albumId = eventCard.dataset.albumId;
  const confirmDelete = confirm('Are you sure you want to delete this album?');
  if (confirmDelete) {
    eventCard.remove();
    console.log(`Deleted album with ID: ${albumId}`);
  }
}
  