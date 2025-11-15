<?php
echo '<div class="tabs">
    <a href="activity" class="tab">Activity</a>
    <a href="friends" class="tab">Friends</a>
    <a href="comments" class="tab">Comments</a>
</div>

<div id="profileContent" class="content mt-2"></div>';
?>
<script>
    async function loadSection(section = "activity", page = 1, pushState = true) {
        const content = document.getElementById("profileContent");
        content.innerHTML = "<i class='text-gray-400 mx-auto my-3'>loading...</i>";

        const path = '<?= BASE_URL ?>';
        const user = encodeURIComponent("<?= $userParam ?>");
        const file = `${path}/pages/profile/${section}.php?u=${user}&page=${page}`;

        try {
            const res = await fetch(file);
            const html = await res.text();
            content.innerHTML = html;
            attachPaginationHandlers(section);
        } catch {
            content.innerHTML = "<p>Error loading section.</p>";
        }

        // highlight active tab
        document.querySelectorAll('.tabs .tab').forEach(tab => {
            tab.classList.toggle('active', tab.getAttribute('href') === section);
        });

        // update browser URL
        if (pushState) {
            const newUrl = `<?php echo BASE_URL; ?>/@<?= $userParam ?>/${section}?page=${page}`;
            history.pushState({ section, page }, "", newUrl);
        }
    }

    function attachPaginationHandlers(section) {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const page = link.getAttribute('data-page');
                loadSection(section, page);
            });
        });
    }

    // handle tab clicks
    document.querySelectorAll('.tabs .tab').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            const section = tab.getAttribute('href');
            loadSection(section, 1);
        });
    });

    // handle back/forward
    window.addEventListener("popstate", e => {
        const state = e.state;
        if (state) loadSection(state.section, state.page, false);
        else loadSection("activity", 1, false);
    });

    // initial load
    const currentPath = window.location.pathname.split("/").pop();
    const params = new URLSearchParams(window.location.search);
    const initialSection = ["activity", "friends", "comments"].includes(currentPath) ? currentPath : "activity";
    const initialPage = params.get("page") || 1;
    loadSection(initialSection, initialPage, false);
</script>