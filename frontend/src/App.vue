<template>
    <QLayout class="shadow-2 rounded-borders">
        <QHeader class="bg-primary text-white"
                 elevated
        >
            <QToolbar>
                <QToolbarTitle>
                    Облачное хранилище
                </QToolbarTitle>
            </QToolbar>
        </QHeader>

        <QPageContainer>
            <QPage>
                <RouterView/>
            </QPage>
        </QPageContainer>
    </QLayout>
</template>
