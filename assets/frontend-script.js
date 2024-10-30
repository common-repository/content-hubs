(function() {
  const chps_els = document.querySelectorAll('.chps-grid');

  chps_els.forEach(function(chps_el){

    const filterButtons = chps_el.querySelectorAll('.chps-filter-item');
    const items = chps_el.querySelectorAll('.chps-grid-item');
    let loadMoreBtn = chps_el.querySelector('.chps-load-more-btn');
    let selectedFilters = [];
    let numberOfVisibleItems = parseInt(chps_el.getAttribute('data-number_of_items'));
    let numberOfLoadMoreItems = numberOfVisibleItems;

    //load-more button required?
    toggleLoadMoreButton();

    // filter
    filterButtons.forEach(function(item, idx){
      item.addEventListener('click', function(event) {

        let filterVal = this.getAttribute('data-filter');

        if(this.classList.contains('active')) {
          // go inactive
          this.classList.remove('active');
          selectedFilters.splice(selectedFilters.indexOf(filterVal), 1);

        } else {
          // go active
          this.classList.add('active');
          selectedFilters.push(filterVal);

        }

        // filter grid
        filterGrid();
        
      }, false);
    });

    items.forEach(function(item, idx){
      item.addEventListener('load', function(event){
        console.log(event);
      })
    });

    loadMoreBtn.addEventListener('click', function(event) {
      numberOfVisibleItems = numberOfVisibleItems + numberOfLoadMoreItems;
      filterGrid();
    });

    // Any operation that needs to be done only after all the fonts have finished loading can go here.
    document.fonts.ready.then(function() {
      items.forEach(function(item, idx) {
        // set auto offset for .chps-text
        setItemTextOffset(item);
      });
    });


    // FUNCTIONS

    /**
     * Filter grid items
     * 
     * Case 1: no filters selected --> show all grid items
     * Case 2: one or more filters selected --> show items with at least one fit (filter terms are "OR"-connected)
     */
    function filterGrid() {
      let showIdx = 0;
      
      // console.log(selectedFilters);

      items.forEach(function(item, idx) {
        let show = false;

        if(selectedFilters.length === 0) {
          //no filters selected
          show = true;
        }else{
          //some filters selected
          selectedFilters.forEach(function(filter) {
            if(item.getAttribute('data-topics').includes(filter)) {
              show = true;
            }else{
              //stay true if set before
              show = (show === true)? true : false;
            }
          });
        }

        // SHOW or HIDE item (respect load-more status)
        if(show === true) {

          showIdx++;

          console.info(showIdx, numberOfVisibleItems);

          if(showIdx <= numberOfVisibleItems) {
            item.classList.remove('chps-load-more-item');
            showItem(item);
          }else{
            hideItem(item);
          }

        }else{

          hideItem(item);

        }

      });

      //load-more button required?
      toggleLoadMoreButton();
    }

    /**
     * Show the item
     * @param item 
     */
    function showItem(item) {
      item.style.display = 'block';

      // set auto offset for .chps-text
      setItemTextOffset(item);

      setTimeout(function(){
        item.classList.remove('chps-hidden');
      }, 10);
      
    }

    /**
     * Hide the item
     * @param item 
     */
    function hideItem(item) {
      item.classList.add('chps-hidden');
      setTimeout(function(){
        item.style.display = 'none';
      }, 300);
    }

    /**
     * Set vertical position of the item title
     * @param item 
     */
    function setItemTextOffset(item) {
      let autoOffset = item.querySelector('.chps-topics').clientHeight + item.querySelector('.chps-headline').clientHeight;

      item.querySelector('.chps-text').style.transform = `translateY(calc(100% - ${autoOffset}px))`;
    }

    /**
     * Toggle visibility of the load-more button
     */
    function toggleLoadMoreButton() {
      if(items.length > numberOfVisibleItems) {
        loadMoreBtn.style.display = 'block';
      }else{
        loadMoreBtn.style.display = 'none';
      }
    }

  });

})();