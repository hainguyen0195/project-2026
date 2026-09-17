const pageAbout = document.querySelector(".main.main-about");
const pageHome = document.querySelector(".main.page-home");

if (pageAbout || pageHome) {
    const titleAni = document.querySelectorAll(".abi-text-ani");
    if (titleAni && titleAni.length > 0) {
        Splitting({
            target: titleAni,
            by: "chars",
            key: null,
        });
        const subTitle = document.querySelectorAll(".abi-text-ani .char");
        gsap.to(subTitle, {
            duration: 1.2,
            ease: "back.inOut",
            delay: 0.2,
            y: 0,
            stagger: 0.06,
            scrollTrigger: {
                trigger: ".sec-abi",
                start: "-20% 80%",
                end: "bottom 80%",
            },
        });

        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: ".abi-wrap",
                scrub: 1,
                duration: 5,
                ease: "Elastic.easeOut",
                start: pageHome ? "20% 70%" : "top 20%",
                end: pageHome ? "bottom 70%" : "bottom 20%",
            },
        });
        tl.to(".abi-ani-text.linex1", {
            duration: 1,
            x: "-25%",
        });
        tl.to(
            ".abi-ani-text.linex2", {
                duration: 1,
                x: "15%",
            },
            0
        );
        tl.to(
            ".abi-ani-text.linex3", {
                duration: 1,
                x: "-5%",
            },
            0
        );
    }
}

/* Cafune new-product title reveal and sticky fade. */
(() => {
    const textWrapper = document.querySelector("#heroTextAnim");
    const abpWrap = document.querySelector(".sec-abp.custom .abp-wrap");
    if (!textWrapper || !abpWrap || typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;

    gsap.registerPlugin(ScrollTrigger);
    textWrapper.innerHTML = textWrapper.textContent.replace(/\S/g, "<span class='letter'>$&</span>");

    gsap.timeline({
        scrollTrigger: {
            trigger: ".sec-abp.custom .abp-title",
            scrub: 1,
            ease: "Elastic.easeOut",
            start: "-30% center",
            end: "80% center",
        },
    }).to("#heroTextAnim .letter", {
        opacity: 1,
        ease: "Power4.easeInOut",
        stagger: 0.04,
    });

    gsap.timeline({
        scrollTrigger: {
            trigger: abpWrap,
            scrub: 1,
            ease: "Elastic.easeOut",
            start: "top top",
            end: "20% top",
        },
    }).to(".sec-abp.custom .abp-title", {
        opacity: 0.2,
        ease: "Power4.easeInOut",
    });

    const sectionTitle = document.querySelector("#comi-new-product-title");
    if (sectionTitle) {
        gsap.set(sectionTitle, { opacity: 0, y: 24 });
        gsap.set(abpWrap, { "--abp-orb-opacity": 0, "--abp-orb-scale": 0.82 });

        gsap.timeline({
            scrollTrigger: {
                trigger: abpWrap,
                scrub: 1,
                start: "top 82%",
                end: "top 38%"
            }
        })
            .to(sectionTitle, { opacity: 1, y: 0, ease: "power2.out" }, 0)
            .to(abpWrap, { "--abp-orb-opacity": 0.4, "--abp-orb-scale": 1, ease: "power2.out" }, 0);

        gsap.timeline({
            scrollTrigger: {
                trigger: abpWrap,
                scrub: 1,
                start: "35% top",
                end: "65% top"
            }
        })
            .to(sectionTitle, { opacity: 0, y: -24, ease: "power2.in" }, 0)
            .to(abpWrap, { "--abp-orb-opacity": 0, "--abp-orb-scale": 1.16, ease: "power2.in" }, 0);
    }
})();

/* Mark the new-product heading only while its CSS sticky state is active. */
(() => {
    const section = document.querySelector(".sec-abp.custom");
    const head = section && section.querySelector(".abp-head");
    if (!section || !head) return;

    function updateStickyTitleState() {
        const stickyTop = parseFloat(window.getComputedStyle(head).top) || 0;
        const headRect = head.getBoundingClientRect();
        const sectionRect = section.getBoundingClientRect();
        const isStuck = headRect.top <= stickyTop + 1 && sectionRect.bottom > stickyTop + headRect.height;
        head.classList.toggle("is-stuck", isStuck);
    }

    updateStickyTitleState();
    window.addEventListener("scroll", updateStickyTitleState, { passive: true });
    window.addEventListener("resize", updateStickyTitleState, { passive: true });
})();
