define(["core/str"], function(str) {
    let url = '';
    let course = document.querySelector('#course-infos');
    let courseid = course.getAttribute('data-courseid');
    let teacherDataContainers = document.querySelectorAll('[data-render="teacher-numbers"]');

    /**
     *
     */
    async function loadTeacherData() {
        if (!url) {
            console.error('URL da API não foi definida.');
            return;
        }

        try {
            const response = await fetch(`${url}?courseid=${courseid}`);

            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
            }

            const teacherData = await response.json();
            renderTeacherData(teacherData);

        } catch (error) {
            console.error('Error loading teacher data:', error);
        }

    }

    /**
     *
     * @param teacherData
     */
    async function renderTeacherData(teacherData) {
        const studentsString = await str.getString('students', 'core');
        const coursesString = await str.getString('courses', 'core');

        teacherData.forEach((teacher, i) => {
            teacherDataContainers[i].innerHTML = `
                <div class="students-number">
                    <i class="fa-solid fa-user-group"></i>
                    <span>${teacher.totalstudents} ${studentsString}</span>
                </div>
                <div class="courses-number">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>${teacher.courses.length} ${coursesString}</span>
                </div>
            `;
        });
    }

    return {
        init: (requestUrl) => {
            url = requestUrl;
            loadTeacherData();
        }
    };
});