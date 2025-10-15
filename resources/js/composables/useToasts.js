import {toast} from "vue-sonner";

export function showToast(toastData) {
    const params = {
        description: toastData.description,
    }

    switch (toastData.type) {
        case 'error':
            toast.error(toastData.message, params);
            break
        case 'success':
            toast.success(toastData.message, params);
            break
        case 'warning':
            toast.warning(toastData.message, params);
            break
        case 'info':
            toast.info(toastData.message, params);
            break
        default:
            toast(toastData.message, params)
    }
}

export function showToasts(toasts) {
    if (!toasts?.length) {
        return
    }

    toasts.forEach((toastData, index) => {
        setTimeout(() => showToast(toastData), index * 300)
    })
}

export function showToastsFromFormData(data) {
    if (data.props?.toasts) {
        showToasts(data.props.toasts)
    }
}

export function showToastsFromInertiaModal(modalRef, data) {
    if (data.props?.toasts) {
        showToasts(data.props?.toasts)
    }
}
