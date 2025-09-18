$(".dropdown.user.user-menu").click(function () {
    $(this).toggleClass("open");
    if ($(this).hasClass("open") == 1)
        // $(".dropdown.user.user-menu .dropdown-menu").css({ "display": "block", "left": "-100px" });
        $(this).find(".dropdown-menu").slideDown();
    else
        // $(".dropdown.user.user-menu .dropdown-menu").css({ "display": "none", "left": "0px" });
        $(this).find(".dropdown-menu").slideUp();

})

$(document).ready(function () {

    if ($('.recipe-slider').find('li').length > 1) {
        $('.recipe-slider').slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            autoplay: true,
            speed: 500,
            autoplaySpeed: 2000,
            infinite: true,
        });
    }

    $("#SkillLevel").select2({
        closeOnSelect: false,
        placeholder: "Select Skill",
        allowClear: true,
    });
    $("#AgeRange").select2({
        closeOnSelect: false,
        placeholder: "Select Age",
        allowClear: true,
    });
    $("#Categories").select2({
        closeOnSelect: false,
        placeholder: "Categories",
        allowClear: true,
    });
    $("#PlanType").select2({
        closeOnSelect: false,
        placeholder: "Select Plan Type",
        allowClear: true,
    });
})