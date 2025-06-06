<!-- includes/loader.php -->
<style>
  #loader {
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    background: #fff;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .spinner-border {
    width: 3rem;
    height: 3rem;
    color: #f59e0b;
  }
</style>

<div id="loader">
  <div class="spinner-border" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<script>
  window.addEventListener('load', () => {
    document.getElementById('loader').style.display = 'none';
  });
</script>
