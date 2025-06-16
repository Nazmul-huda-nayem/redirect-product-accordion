"use strict";
(()=>{
  function bwdacAccordionMaker(accordion) {
    let accordionItems = accordion.querySelectorAll(".woocpa-Accordion-default");
  
    for (let singleItem of accordionItems) {
      let itemButton = singleItem.querySelector("div");
      itemButton.addEventListener("click", function () {
        let buttonParent = this.parentElement;
  
        if (buttonParent.classList.contains("active")) {
          buttonParent.classList.remove("active");
          for (let item of accordionItems) {
            item.classList.remove("active");
          }
        } else {
          for (let item of accordionItems) {
            item.classList.remove("active");
          }
          buttonParent.classList.add("active");
        }
      });
    }
  
    function documentClickToActiveClass(e) {
      let isClick = accordion.contains(e.target);
      if (!isClick) {
        for (let singleItem of accordionItems) {
          singleItem.classList.remove("active");
        }
      }
    }
    document.addEventListener("click", documentClickToActiveClass);
  }
  // accordion plyer
  const bwdacAccordionPlayer = () => {
    let allAccordionCommon = document.querySelectorAll(".woocpa-accordion-all>*");
    for (let item of allAccordionCommon) {
      bwdacAccordionMaker(item);
    }
  };
  
  //===================================================
  // editMode active or not
  let bwdacAccordionEditModeObserver = (getEditMode) => {
    // elementor render observing
    const bwdacAccordionObserver = new MutationObserver((mutations) => {
      mutations.map((record) => {
        if (record.addedNodes.length) {
          record.addedNodes.forEach((singleItem) => {
            if (singleItem.nodeName == "DIV") {
              if (singleItem.querySelector(".woocpa-accordion-all>*")) {
                let observedItem = singleItem.querySelector(
                  ".woocpa-accordion-all>*"
                );
                bwdacAccordionMaker(observedItem);
              }
            }
          });
        }
      });
    });
  
    bwdacAccordionObserver.observe(getEditMode, {
      subtree: true,
      childList: true,
    });
  };
  
  // edit mode checker
  (() => {
    let bwdacMyInterValId;
    if (
      document.querySelector(".elementor-edit-area") ||
      document.querySelector(".woocpa-accordion-all>*")
    ) {
      bwdacAccordionPlayer();
    } else {
      bwdacMyInterValId = setInterval(() => {
        let bwdacElementorEditArea = document.querySelector(".elementor-edit-area") || document.querySelector(".page");
        if (bwdacElementorEditArea) {
          clearInterval(bwdacMyInterValId);
          // play ===============
          bwdacAccordionEditModeObserver(bwdacElementorEditArea);
        }
      }, 300);
    }
  })();






})()
