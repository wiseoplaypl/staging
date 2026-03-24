var lazyLoadInstance = new LazyLoad({
  elements_selector: "img.ndk-lazy",
  data_src: "thumb",
  unobserve_entered: true, // <- Avoid executing the function multiple times
  //callback_enter: executeLazyFunction,
});

function findAncestor(el, cls) {
  while ((el = el.parentElement) && !el.classList.contains(cls));
  return el;
}

function executeLazyFunction(element) {
  grid = findAncestor(element, "image-library");
  resizeMasonryItem(grid, element);
}
const lazyUnload = function (event) {
  let lazyiedImages = [].slice.call(document.querySelectorAll("img.ndk-lazy"));
  lazyiedImages.forEach(function (lazyIedmage) {
    lazyIedmage.classList.add("ndk-lazy");
    lazyIedmage.classList.remove("ndk-lazyied");
    lazyIedmage.classList.remove("loaded");
    lazyIedmage.classList.remove("entered");
    lazyIedmage.src = lazyImgDefault;
    delete lazyIedmage.dataset.llStatus;
  });

  lazyLoadInstance.update();
};
